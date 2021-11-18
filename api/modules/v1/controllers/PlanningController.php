<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\actions\planning\CreateAction;
use api\modules\v1\controllers\actions\planning\IndexAction;
use common\access\Rbac;
use common\forms\training\TrainingCreateForm;
use common\forms\training\TrainingEditForm;
use common\forms\training\TrainingMemberForm;
use common\forms\training\TrainingRejectMoveRequestForm;
use common\forms\TrainingRatingForm;
use common\models\Meeting;
use common\models\Program;
use common\models\SessionRating;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserMeeting;
use common\models\UserTraining;
use common\repositories\MeetingRepository;
use common\repositories\TrainingRepository;
use common\repositories\UserMeetingRepository;
use common\repositories\UserRepository;
use common\repositories\UserTrainingRepository;
use common\useCases\TrainingManageCase;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class PlanningController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;
    public $trainingRepo;
    public $userTrainingRepo;
    public $userRepo;
    public $meetingRepo;
    public $userMeetingRepo;

    public function __construct(
        $id,
        $module,
        TrainingManageCase $useCase,
        UserRepository $userRepo,
        TrainingRepository $trainingRepo,
        UserTrainingRepository $userTrainingRepo,
        MeetingRepository $meetingRepo,
        UserMeetingRepository $userMeetingRepo,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->trainingRepo = $trainingRepo;
        $this->userTrainingRepo = $userTrainingRepo;
        $this->userRepo = $userRepo;
        $this->useCase = $useCase;
        $this->meetingRepo = $meetingRepo;
        $this->userMeetingRepo = $userMeetingRepo;
    }

    /**
     * {@inheritDoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'rate' => ['POST', 'OPTIONS'],
            'move' => ['PATCH', 'OPTIONS'],
            'cancel' => ['PATCH', 'OPTIONS'],
            'take' => ['PATCH', 'OPTIONS'],
            'confirm' => ['PATCH', 'OPTIONS'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'index', 'create', 'cancel', 'move', 'rate', 'take', 'confirm', 'reject-move-request'
                    ],
                    'roles' => [Rbac::ROLE_EMP],
                ],
                [
                    'allow' => true,
                    'actions' => [
                        'index', 'create', 'cancel', 'move', 'rate', 'confirm', 'reject-move-request'
                    ],
                    'roles' => [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                ],
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'class' => IndexAction::class,
                    'modelClass' => $this->modelClass
                ],
                'view' => false,
                'create' => [
                    'class' => CreateAction::class,
                    'modelClass' => $this->modelClass
                ],
                'update' => false,
                'delete' => false
            ]
        );
    }

    /**
     * Перенос запланированной сессии
     *
     * @param $training_uuid
     * @return TrainingEditForm|TrainingSession
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws ForbiddenHttpException
     */
    public function actionMove($training_uuid)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $model = $this->trainingRepo->get($training_uuid);


        if ($model->isFree()) {
            $form = new TrainingEditForm($user, $model);
        } else {
            $form = new TrainingCreateForm($user);
            /** @var UserTraining $invitedUserTraining */
            $invitedUserTraining = $model->getUserAssignments()->where([
                '!=',
                'user_uuid',
                $user->user_uuid
            ])->one();

            $form->invited_uuid = $invitedUserTraining->user_uuid;
            $form->moved_from = $model->training_uuid;
            $form->moved_by_role = $user->role;
            $form->scenario = 'move';
        }

        if ($this->validateBody($form)) {
            $this->checkAccess(
                $this->action->id,
                $model,
                [
                    'start_at' => $form->start_at,
                    'duration' => $form->duration
                ]
            );

            try {
                if ($model->isFree()) {
                    $this->useCase->moveFree($model, $form);
                } else {
                    $model->sendMoveNotification($form);
                    $this->trainingRepo->save($model);
                    $model = $this->useCase->create($form);
                }
                \Yii::$app->response->setStatusCode(200);
                $model->refresh();

                return $model;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Отклонение запроса на перенос запланированной сессии
     *
     * @param $training_uuid
     * @return TrainingRejectMoveRequestForm|TrainingSession
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionRejectMoveRequest($training_uuid)
    {
        /** @var TrainingSession $model */
        $model = $this->trainingRepo->get($training_uuid);

        if (!$model->isNotConfirm()) {
            throw new ForbiddenHttpException('Сессия должна быть в статусе "не подтверждена".');
        }

        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $form = new TrainingRejectMoveRequestForm($user);
        if ($this->validateBody($form)) {
            try {
                $model->sendRejectedMoveNotification($form);
                $this->useCase->rejectMoveRequest($model, $form);

                return $model;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Отмена запланированной сессии
     *
     * @param $training_uuid
     * @return TrainingSession
     * @throws BadRequestHttpException
     */
    public function actionCancel($training_uuid): TrainingSession
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;
        $trainingSession = $this->trainingRepo->get($training_uuid);
        $form = new TrainingEditForm($user, $trainingSession);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->cancel($form);
                \Yii::$app->response->setStatusCode(200);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $trainingSession;
    }

    /**
     * Добавление оценки сессии
     * @param $training_uuid
     * @return TrainingRatingForm|TrainingSession
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRate($training_uuid)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        /** @var TrainingSession $model */
        $model = $this->trainingRepo->get($training_uuid);
        /** @var UserTraining $assignment */
        $assignment = $this->userTrainingRepo->get($user->user_uuid, $model->training_uuid);
        /** @var UserTraining $assignmentOther */
        $assignmentOther = $this->userTrainingRepo->getOther($user->user_uuid, $model->training_uuid);

        if ($model->isConfirm() && $model->isExpired()) {
            throw new ForbiddenHttpException('Сессия отменена коучем/ментором.');
        } else {
            if (!$model->isStatusCompleted()) {
                throw new ForbiddenHttpException('Оценивать можно только завершенные сессии.');
            }
        }

        /** @var Meeting $meeting */
        $meeting = Meeting::find()->andWhere(['training_uuid' => $model->training_uuid])->one();
        if (!empty($meeting)) {
            /** @var UserMeeting $userMeeting */
            $userMeeting = $this->userMeetingRepo->getByMeetingAndUser($meeting->meeting_uuid, $user->user_uuid);
            if (!empty($userMeeting) && !$userMeeting->isJoined()) {
                throw new ForbiddenHttpException('Вы не можете оценивать сессию, в которой не принимали участие.');
            }
        }

        /** @var SessionRating $sessionRating */
        $sessionRating = $model->getSessionRatings()
            ->andWhere(['=', 'user_uuid', $assignmentOther->user_uuid])
            ->one();
        if (!empty($sessionRating)) {
            throw new ForbiddenHttpException('Сессия уже оценена вами.');
        }

        /** @var TrainingRatingForm $form */
        $form = new TrainingRatingForm($user, $model);

        if ($this->validateBody($form)) {
            try {
                if (in_array($user->role, [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR], true)) {
                    $this->useCase->complete($form, $assignment, $assignmentOther);
                }

                $this->useCase->addRate($form, $assignment, $assignmentOther);
                \Yii::$app->response->setStatusCode(201);
                $model->refresh();
                return $model;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Запись сотрудника в свободный слот и отправка запроса на подтверждение ментору/коучу
     *
     * @param $training_uuid
     * @return TrainingMemberForm|TrainingSession
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionTake($training_uuid)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $model = $this->trainingRepo->get($training_uuid);

        $this->checkAccess(
            $this->action->id,
            $model,
            [
                'start_at' => $model->getNormalizedStartTime(),
                'duration' => $model->duration
            ]
        );

        $form = new TrainingMemberForm($user, $model);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->addMember($form);
                $this->useCase->confirm($model, $user);
                \Yii::$app->response->setStatusCode(201);
                $model->refresh();
                return $model;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Подтверждение запланированной сессии
     *
     * @param $training_uuid
     * @return TrainingEditForm|TrainingSession
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionConfirm($training_uuid)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $model = $this->trainingRepo->get($training_uuid);

        $this->checkAccess($this->action->id, $model);

        $form = new TrainingEditForm($user);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->confirm($model, $user);
                $model->refresh();
                return $model;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Проверяем доступ к действиям
     * @param string $action
     * @param null $model
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function checkAccess($action, $model = null, $params = null): void
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        if (
            (in_array(
                $action,
                ['change-status', 'program', 'invite', 'contact']
            )) && $model->client_uuid !== $user->client_uuid
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if (
            in_array(
                $action,
                [
                    'connected',
                    'requesting',
                    'approve-request',
                    'decline-request',
                    'contact'
                ]
            )
        ) {
            if (
                !$user->getEmployees()
                    ->andWhere(['user_uuid' => $model->user_uuid])
                    ->exists()
            ) {
                throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
            }
        }

        if ($action === 'take') {
            /** @var User $coachOrMentor */
            $coachOrMentor = $model->coachOrMentor;
            $programUuid = $coachOrMentor->isUserRoleCoach() ? Program::COACH_UUID : Program::MENTOR_UUID;
            $user->checkingEmployeeForLimitPlannedSession($programUuid);

            if (!$model->isStatusFree()) {
                throw new ForbiddenHttpException("Сессия должна быть в статусе 'свободное время'.");
            }

            if (!$user->isSessionTimeFree($params['start_at'], $params['duration'], $model)) {
                $errorMessage = $user->getEmployeeSessionBusyTimeFreeSlotErrorMessage();
                throw new ForbiddenHttpException($errorMessage);
            }
        }

        if ($action === 'move') {
            /** @var UserTraining $userTraining */
            $userTraining = $this->userTrainingRepo->get($user->user_uuid, $model->training_uuid);
            if (empty($userTraining)) {
                throw new ForbiddenHttpException('Нельзя переносить сессии, в которых не принимаешь участие.');
            }

            if (!$model->isStatusFree()) {
                /** @var UserTraining $userTraining */
                $otherUserTraining = $this->userTrainingRepo->getOther($user->user_uuid, $model->training_uuid);

                if ($user->isUserRoleEmployee()) {
                    /** @var User $employeeUser */
                    $employeeUser = $user;
                    /** @var User $mentorOrCoachUser */
                    $mentorOrCoachUser = User::findOne($otherUserTraining->user_uuid);
                } else {
                    /** @var User $employeeUser */
                    $employeeUser = User::findOne($otherUserTraining->user_uuid);
                    /** @var User $mentorOrCoachUser */
                    $mentorOrCoachUser = $user;
                }

                if (!$employeeUser->isSessionTimeFree($params['start_at'], $params['duration'], $model)) {
                    $errorMessage = $user->getEmployeeSessionBusyTimeErrorMessage();
                    throw new ForbiddenHttpException($errorMessage);
                }

                if (!$mentorOrCoachUser->isSessionTimeFree($params['start_at'], $params['duration'], $model)) {
                    $errorMessage = $mentorOrCoachUser->getMentorSessionBusyTimeErrorMessage();
                    throw new ForbiddenHttpException($errorMessage);
                }
            }
        }
    }
}
