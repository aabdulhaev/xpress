<?php

declare(strict_types=1);

namespace common\useCases;

use common\forms\meeting\GroupMeetingCreateForm;
use common\forms\meeting\GroupMeetingUpdateForm;
use common\forms\meeting\MeetingCheckConfirmForm;
use common\forms\meeting\MeetingCreateForm;
use common\forms\meeting\MeetingGroupJoinForm;
use common\forms\meeting\MeetingMakeConfirmForm;
use common\models\Meeting;
use common\models\Subject;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserMeeting;
use common\models\UserTraining;
use common\repositories\MeetingRepository;
use common\repositories\TrainingRepository;
use common\repositories\UserMeetingRepository;
use common\repositories\UserTrainingRepository;
use common\services\MeetingManager;
use common\services\TransactionManager;
use yii\db\ActiveQuery;
use yii\web\ForbiddenHttpException;

class MeetingManageCase
{
    public $repo;
    public $trainingRepo;
    public $service;
    private $transaction;
    private $userMeetingRepo;
    private $userTrainingRepo;

    public function __construct(
        MeetingRepository      $repo,
        TransactionManager     $transaction,
        MeetingManager         $service,
        TrainingRepository     $trainingRepo,
        UserMeetingRepository  $userMeetingRepo,
        UserTrainingRepository $userTrainingRepo
    )
    {
        $this->repo = $repo;
        $this->trainingRepo = $trainingRepo;
        $this->service = $service;
        $this->transaction = $transaction;
        $this->userMeetingRepo = $userMeetingRepo;
        $this->userTrainingRepo = $userTrainingRepo;
    }

    /**
     * @param MeetingCreateForm $form
     * @param User $user
     * @return Meeting
     * @throws \Exception
     */
    public function create(MeetingCreateForm $form, User $user): Meeting
    {
        $training = $this->trainingRepo->get($form->training_uuid);

        $model = Meeting::create($form->training_uuid, $form->start_at);

        $this->transaction->wrap(function () use ($model, $training, $user) {
            /** @var Subject $subject */
            $subject = $training->getSubject()->one();
            $title = !empty($subject) ? $subject->title : "Сессия";
            $this->service->createMeeting($training, $title);
            $training->toStart();
            $this->repo->save($model);
            $this->trainingRepo->save($training);

            $usersMeeting[] = $user->user_uuid;

            /** @var UserTraining $assignmentOther */
            $assignmentOther = $this->userTrainingRepo->getOther($user->user_uuid, $training->training_uuid);
            if (!empty($assignmentOther)) {
                $usersMeeting[] = $assignmentOther->user_uuid;
            }

            $this->addUpdateAuthUsers($usersMeeting, $model, UserMeeting::STATUS_CONFIRMED);
        });

        return $model;
    }

    /**
     * @param TrainingSession $training
     * @param User $user
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function joinToBasicMeeting(TrainingSession $training, User $user): string
    {
        if (!$user->isUserRoleEmployee()) {
            $url = $this->service->joinMdMeeting($training->training_uuid, $user->first_name);
        } else {
            $url = $this->service->joinAtMeeting($training->training_uuid, $user->first_name);
        }

        /** @var Meeting $meeting */
        $meeting = $this->repo->getByTraining($training->training_uuid);
        /** @var UserMeeting $userMeeting */
        $userMeeting = $this->userMeetingRepo->getByCondition([
            'meeting_uuid' => $meeting->meeting_uuid,
            'user_uuid' => $user->user_uuid
        ]);
        $userMeeting->toJoined();
        $this->userMeetingRepo->save($userMeeting);

        return $url;
    }

    /**
     * @param GroupMeetingCreateForm $form
     * @param User $user
     * @return Meeting
     * @throws \Exception
     */
    public function createGroupMeeting(GroupMeetingCreateForm $form, User $user): Meeting
    {
        /** @var Meeting $model */
        $model = Meeting::createGroupMeeting(
            $form->start_at,
            $form->end_at,
            $form->title,
            $form->description
        );

        $this->transaction->wrap(function () use ($model, $form, $user) {
            $this->repo->save($model);

            $form->coaches = $form->coaches ? : [];
            $form->employees = $form->employees ? : [];
            $userUuids = array_merge(
                [$user->user_uuid],
                $form->coaches,
                $form->employees
            );

            $this->addUpdateAuthUsers($userUuids, $model);

            if ($form->emails) {
                // add not auth users
                $this->addUpdateNotAuthUsers($form->emails, $model);
            }
        });

        return $model;
    }

    /**
     * @param GroupMeetingUpdateForm $form
     * @param User $user
     * @return Meeting
     * @throws \Exception
     */
    public function updateGroupMeeting(GroupMeetingUpdateForm $form, User $user): Meeting
    {
        $model = $form->getMeeting();

        if ($model->checkMeetingTimeIsChanged($form->start_at, $form->end_at)) {
            $previousStartDate = $model->formatStartDate();
            $previousStartTime = $model->formatStartTime();
            /** @var ActiveQuery $userMeetingsQuery */
            $userMeetingsQuery = $model->getUserMeetings();
            foreach ($userMeetingsQuery->each() as $userMeeting) {
                /** @var UserMeeting $userMeeting */
                $model->sendMoveMeetingNotification($userMeeting, $previousStartDate, $previousStartTime);
            }
        }

        $model->start_at = $form->start_at;
        $model->end_at = $form->end_at;
        $model->title = $form->title;
        $model->description = $form->description;

        $model->sendAdminCreateGroupMeetingNotification();

        $this->transaction->wrap(function () use ($model, $form, $user) {
            $this->repo->save($model);

            $form->coaches = $form->coaches ? : [];
            $form->employees = $form->employees ? : [];
            $userUuids = array_merge(
                [$user->user_uuid],
                $form->coaches,
                $form->employees
            );
            $this->addUpdateAuthUsers($userUuids, $model);

            $this->addUpdateNotAuthUsers(($form->emails ? : []), $model);
        });

        return $model;
    }

    /**
     * @param Meeting $meeting
     */
    public function deleteGroupMeeting(Meeting $meeting): void
    {
        $meeting->toDeleted();

        $meeting->sendCancelMeetingNotification();

        $this->repo->save($meeting);
    }

    /**
     * @param MeetingGroupJoinForm $form
     * @return string
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function joinToGroupMeeting(Meeting $meeting, MeetingGroupJoinForm $form): string
    {
        $user = null;
        if (!empty($form->user_uuid)) {
            /** @var User $user */
            $user = User::findOne($form->user_uuid);
            /** @var UserMeeting $userMeeting */
            $userMeeting = $this->userMeetingRepo->getByMeetingAndUser($meeting->meeting_uuid, $form->user_uuid);
        } else if (!empty($form->token)) {
            /** @var UserMeeting $userMeeting */
            $userMeeting = $this->userMeetingRepo->getByToken($form->token);
            /** @var User $user */
            $user = $userMeeting->user;
        }

        if ((!empty($user) && !$user->isMeetingCreator()) || empty($user)) {
            if (!empty($userMeeting)) {
                /** @var UserMeeting $adminUserMeeting */
                $adminUserMeeting = $this->userMeetingRepo->getByMeetingAndUser($meeting->meeting_uuid, $meeting->created_by);
                if (!$adminUserMeeting->isJoined()) {
                    throw new ForbiddenHttpException('Вебинар будет доступен после того как ведущий присоединится к нему.');
                }
            }
        }

        $this->service->createGroupMeeting($meeting);

        if (!empty($user)) {
            if ($user->isMeetingCreator()) {
                $url = $this->service->joinMdMeeting($meeting->meeting_uuid, $user->first_name);
            } else {
                $url = $this->service->joinAtMeeting($meeting->meeting_uuid, $user->first_name);
            }
        } else {
            $url = $this->service->joinAtMeeting($meeting->meeting_uuid, 'Гость');
        }

        if (!empty($userMeeting)) {
            $userMeeting->toJoined();
            $this->userMeetingRepo->save($userMeeting);
        }

        return $url;
    }

    /**
     * @param array $userUuids
     * @param Meeting $meeting
     * @param int $userMeetingStatus
     * @throws \yii\base\Exception
     */
    private function addUpdateAuthUsers(array $userUuids, Meeting $meeting, int $userMeetingStatus = UserMeeting::STATUS_NOT_INVITED): void
    {
        $existingUsers = [];

        foreach ($userUuids as $userUuid) {
            /** @var UserMeeting $userMeeting */
            $userMeeting = $this->userMeetingRepo->getByMeetingAndUser($meeting->meeting_uuid, $userUuid);
            if ($userMeeting) {
                $userMeeting->status = ($userMeeting->status == UserMeeting::STATUS_DELETED ? UserMeeting::STATUS_NOT_INVITED : $userMeeting->status);
            } else {
                $userMeeting = UserMeeting::create($meeting->meeting_uuid, $userUuid, null, $userMeetingStatus);
            }
            $this->userMeetingRepo->save($userMeeting);

            $existingUsers[] = $userUuid;
        }

        foreach ($meeting->userMeetings as $userMeeting) {
            /* @var $userMeeting UserMeeting */
            if (
                !in_array($userMeeting->user_uuid, $existingUsers) &&
                $userMeeting->status != UserMeeting::STATUS_DELETED &&
                !empty($userMeeting->user_uuid)
            ) {
                $userMeeting->status = UserMeeting::STATUS_DELETED;
                $this->userMeetingRepo->save($userMeeting);
            }
        }
    }

    /**
     * @param $emails
     * @param $meetingUuid
     * @throws \yii\base\Exception
     */
    private function addUpdateNotAuthUsers(array $emails, Meeting $meeting, int $userMeetingStatus = UserMeeting::STATUS_NOT_INVITED): void
    {
        $existingEmails = [];
        if (!empty($emails)) {
            foreach ($emails as $email) {
                /** @var UserMeeting $userMeeting */
                $userMeeting = $this->userMeetingRepo->getByMeetingAndEmail($meeting->meeting_uuid, $email);
                if ($userMeeting) {
                    $userMeeting->status = ($userMeeting->status == UserMeeting::STATUS_DELETED ? UserMeeting::STATUS_NOT_INVITED : $userMeeting->status);
                } else {
                    $userMeeting = UserMeeting::create($meeting->meeting_uuid, null, $email, $userMeetingStatus);
                }
                $this->userMeetingRepo->save($userMeeting);

                $existingEmails[] = $email;
            }
        }

        foreach ($meeting->userMeetings as $userMeeting) {
            /* @var $userMeeting UserMeeting */
            if (
                !in_array($userMeeting->email, $existingEmails) &&
                $userMeeting->status != UserMeeting::STATUS_DELETED &&
                !empty($userMeeting->email)
            ) {
                $userMeeting->status = UserMeeting::STATUS_DELETED;
                $this->userMeetingRepo->save($userMeeting);
            }
        }
    }

    /**
     * @param MeetingMakeConfirmForm $form
     */
    public function makeConfirm(MeetingMakeConfirmForm $form): void
    {
        /** @var UserMeeting $userMeeting */
        $userMeeting = $this->userMeetingRepo->getByToken($form->token);
        $userMeeting->status = UserMeeting::STATUS_CONFIRMED;
        $this->userMeetingRepo->save($userMeeting);
    }

    /**
     * @param MeetingCheckConfirmForm $form
     * @return bool
     */
    public function checkConfirm(MeetingCheckConfirmForm $form): bool
    {
        /** @var UserMeeting $userMeeting */
        $userMeeting = $this->userMeetingRepo->getByToken($form->token);
        return !$userMeeting->isNotInvited();
    }
}
