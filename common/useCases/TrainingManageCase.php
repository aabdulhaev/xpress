<?php

declare(strict_types=1);

namespace common\useCases;

use common\access\Rbac;
use common\forms\training\TrainingCreateForm;
use common\forms\training\TrainingEditForm;
use common\forms\training\TrainingMemberForm;
use common\forms\training\TrainingRejectMoveRequestForm;
use common\forms\TrainingRatingForm;
use common\forms\user\UserTrainingCreateForm;
use common\models\Program;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserProgram;
use common\models\UserTraining;
use common\repositories\MeetingRepository;
use common\repositories\TrainingRepository;
use common\repositories\UserProgramRepository;
use common\repositories\UserTrainingRepository;
use common\services\TransactionManager;

class TrainingManageCase
{
    public $repo;
    public $assignmentsRepo;
    public $meetingRepo;
    public $userProgramRepo;
    private $transaction;

    public function __construct(
        TrainingRepository $repo,
        UserTrainingRepository $assignmentsRepo,
        MeetingRepository $meetingRepo,
        UserProgramRepository $userProgramRepo,
        TransactionManager $transaction
    )
    {
        $this->repo = $repo;
        $this->assignmentsRepo = $assignmentsRepo;
        $this->meetingRepo = $meetingRepo;
        $this->userProgramRepo = $userProgramRepo;
        $this->transaction = $transaction;
    }

    /**
     * @param TrainingCreateForm $form
     * @return TrainingSession
     * @throws \Exception
     */
    public function create(TrainingCreateForm $form): TrainingSession
    {
        $model = TrainingSession::create($form);

        $this->transaction->wrap(function () use ($model, $form) {
            $ownerTrainingForm = new UserTrainingCreateForm($form->owner_uuid);
            $ownerTrainingForm->status = UserTraining::STATUS_CONFIRM;
            $ownerTrainingForm->comment = $form->comment;
            $ownerTrainingForm->scenario = 'assignment';

            /** @var UserTraining $ownerAssignment */
            $ownerAssignment = UserTraining::create($ownerTrainingForm);
            $model->assignMember($ownerAssignment);

            if ($model->status === TrainingSession::STATUS_NOT_CONFIRM) {
                $invitedTrainingForm = new UserTrainingCreateForm($form->invited_uuid);
                $invitedTrainingForm->status = UserTraining::STATUS_NOT_CONFIRM;
                $invitedTrainingForm->comment = $form->comment;
                $invitedTrainingForm->scenario = 'assignment';

                /** @var UserTraining $invitedAssignment */
                $invitedAssignment = UserTraining::create($invitedTrainingForm);
                $model->assignMember($invitedAssignment);
            } elseif ($model->status === TrainingSession::STATUS_FREE) {
                $model->program_uuid = $model->coachOrMentor->role === Rbac::ROLE_MENTOR ?
                    Program::MENTOR_UUID :
                    Program::COACH_UUID;
            }

            if($form->scenario !== 'move'){
                $model->sendCreateSessionPlanning();
            }

            $this->repo->save($model);

            if ($form->scenario === 'move') {
                /** @var TrainingSession $oldTraining */
                $oldTraining = $model->getMovedFrom()->one();
                /** @var UserTraining $ownerAssignment */
                $ownerAssignment = $this->assignmentsRepo->get($form->owner_uuid, $oldTraining->training_uuid);
                /** @var UserTraining $invitedAssignment */
                $invitedAssignment = $this->assignmentsRepo->getOther($form->owner_uuid, $oldTraining->training_uuid);

                $ownerAssignment->toDeleted();
                $this->assignmentsRepo->save($ownerAssignment);

                $invitedAssignment->toDeleted();
                $this->assignmentsRepo->save($invitedAssignment);

                $oldTraining->toDeleted();
                $this->repo->save($oldTraining);
            }
        });

        return $model;
    }

    /**
     * @param TrainingEditForm $form
     * @throws \Exception
     */
    public function cancel(TrainingEditForm $form): void
    {
        $session = $form->trainingSession;
        $assignment = $this->assignmentsRepo->get(
            $form->editor->user_uuid,
            $session->training_uuid
        );
        $assignmentOther = false;
        if ($session->status !== TrainingSession::STATUS_FREE) {
            $assignmentOther = $this->assignmentsRepo->getOther(
                $form->editor->user_uuid,
                $session->training_uuid
            );
        }

        $this->transaction->wrap(function () use ($session, $assignment, $assignmentOther, $form) {
            if ($assignmentOther) {
                $session->toCancel($form);
                $assignment->toCancel();
                $assignment->addComment($form->comment);
                $assignmentOther->toCancel();
                $this->assignmentsRepo->save($assignment);
                $this->assignmentsRepo->save($assignmentOther);
                $this->repo->save($session);
            } else {
                $assignment->softDelete();
                $session->softDelete();
            }
        });
    }

    /**
     * @param TrainingSession $session
     * @param User $user
     * @throws \Exception
     */
    public function confirm(TrainingSession $session, User $user): void
    {
        $assignment = $this->assignmentsRepo->get($user->user_uuid, $session->training_uuid);
        $assignmentOther = $this->assignmentsRepo->getOther($user->user_uuid, $session->training_uuid);

        $this->transaction->wrap(function () use ($session, $assignment, $assignmentOther) {

            $assignment->toConfirm();
            $session->sendConfirmNotification($assignment->user);

            $this->assignmentsRepo->save($assignment);

            if ($assignmentOther->status === UserTraining::STATUS_CONFIRM) {
                $session->toConfirm();
                $this->repo->save($session);
            }
        });
    }

    /**
     * @param TrainingSession $session
     * @param TrainingEditForm $form
     * @throws \Exception
     */
    public function move(TrainingSession $session, TrainingEditForm $form): void
    {
        $assignmentUserTraining = $this->assignmentsRepo->get($form->editor->user_uuid, $session->training_uuid);
        $assignmentOtherUserTraining = $this->assignmentsRepo->getOther(
            $form->editor->user_uuid,
            $session->training_uuid
        );
        $this->transaction->wrap(
            function () use ($session, $assignmentUserTraining, $assignmentOtherUserTraining, $form) {
                $session->move($form->start_at, $form->duration);
                $session->toNonConfirm();
                $assignmentUserTraining->addComment($form->comment);
                $assignmentOtherUserTraining->toNotConfirm();
                $this->assignmentsRepo->save($assignmentUserTraining);
                $this->assignmentsRepo->save($assignmentOtherUserTraining);
                $this->repo->save($session);
            }
        );
    }

    /**
     * @param TrainingSession $session
     * @param TrainingEditForm $form
     * @throws \Exception
     */
    public function moveFree(TrainingSession $session, TrainingEditForm $form): void
    {
        $this->transaction->wrap(function () use ($session, $form) {
            $session->move($form->start_at, intval($form->duration));
            $this->repo->save($session);
        });
    }

    /**
     * @param TrainingRatingForm $form
     * @param UserTraining $assignment
     * @param UserTraining $assignmentOther
     * @throws \Exception
     */
    public function complete(TrainingRatingForm $form, UserTraining $assignment, UserTraining $assignmentOther): void
    {
        $this->transaction->wrap(function () use ($form, $assignment, $assignmentOther) {
            $form->training->toComplete();
            $this->repo->save($form->training);

            $assignment->toNotEstimate();
            $this->assignmentsRepo->save($assignment);

            $assignmentOther->toNotEstimate();
            $this->assignmentsRepo->save($assignmentOther);

            if ($form->training->meeting) {
                $form->training->meeting->toComplete();
                $this->meetingRepo->save($form->training->meeting);
            }
        });
    }

    /**
     * @param TrainingRatingForm $form
     * @param UserTraining $assignment
     * @param UserTraining $assignmentOther
     * @throws \Exception
     */
    public function addRate(TrainingRatingForm $form, UserTraining $assignment, UserTraining $assignmentOther): void
    {
        $this->transaction->wrap(function () use ($form, $assignment, $assignmentOther) {
            $form->training->assignRating(
                $assignmentOther->user_uuid,
                $form->rate,
                $form->comment,
                $form->subjects
            );

            $assignment->toEstimate();
            $this->assignmentsRepo->save($assignment);

            $form->training->toRated();
            $form->training->sendEmailRate($form);

            $this->repo->save($form->training);
        });
    }

    /**
     * @param TrainingMemberForm $form
     * @throws \Exception
     */
    public function addMember(TrainingMemberForm $form): void
    {
        $this->transaction->wrap(function () use ($form) {
            $userTrainingForm = new UserTrainingCreateForm($form->user->user_uuid);
            $userTrainingForm->status = $form->status;
            $userTrainingForm->scenario = 'assignment';
            $userTraining = UserTraining::create($userTrainingForm);

            $form->training->assignMember($userTraining);
            $this->repo->save($form->training);
        });
    }

    /**
     * @param TrainingSession $rejectedSession
     * @param TrainingRejectMoveRequestForm $form
     * @throws \Exception
     */
    public function rejectMoveRequest(TrainingSession $rejectedSession, TrainingRejectMoveRequestForm $form): void
    {
        $this->transaction->wrap(function () use ($rejectedSession, $form) {
            $rejectedSession->move_request_reject_comment = $form->comment;
            $rejectedSession->status = TrainingSession::STATUS_DELETED;
            $this->repo->save($rejectedSession);

            /** @var UserTraining $ownerAssignment */
            $rejectedOwnerAssignment = $this->assignmentsRepo
                ->get($rejectedSession->created_by, $rejectedSession->training_uuid);
            $rejectedOwnerAssignment->status = UserTraining::STATUS_DELETED;
            $this->assignmentsRepo->save($rejectedOwnerAssignment);

            /** @var UserTraining $invitedAssignment */
            $rejectedInvitedAssignment = $this->assignmentsRepo
                ->getOther($rejectedSession->created_by, $rejectedSession->training_uuid);
            $rejectedInvitedAssignment->status = UserTraining::STATUS_DELETED;
            $this->assignmentsRepo->save($rejectedInvitedAssignment);

            /** @var TrainingSession $oldTraining */
            $oldTraining = $rejectedSession->getMovedFrom()->one();
            $ownerUser = User::findOne(['user_uuid' => $oldTraining->created_by]);
            /** @var UserTraining $ownerAssignment */
            $ownerAssignment = $this->assignmentsRepo
                ->get($oldTraining->created_by, $oldTraining->training_uuid);
            /** @var UserTraining $invitedAssignment */
            $invitedAssignment = $this->assignmentsRepo
                ->getOther($oldTraining->created_by, $oldTraining->training_uuid);

            $oldTraining->status = TrainingSession::STATUS_NOT_CONFIRM;
            $this->repo->save($oldTraining);

            $ownerAssignment->status = UserTraining::STATUS_CONFIRM;
            $this->assignmentsRepo->save($ownerAssignment);

            $invitedAssignment->status = UserTraining::STATUS_NOT_CONFIRM;
            $this->assignmentsRepo->save($invitedAssignment);
        });
    }
}
