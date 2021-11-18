<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\actions\meeting\IndexAction;
use common\access\Rbac;
use common\filters\Cors;
use common\forms\meeting\GroupMeetingCreateForm;
use common\forms\meeting\GroupMeetingUpdateForm;
use common\forms\meeting\MeetingCheckConfirmForm;
use common\forms\meeting\MeetingCreateForm;
use common\forms\meeting\MeetingGroupJoinForm;
use common\forms\meeting\MeetingMakeConfirmForm;
use common\forms\meeting\MeetingViewForm;
use common\models\Meeting;
use common\models\Section;
use common\models\TrainingSession;
use common\models\User;
use common\repositories\MeetingRepository;
use common\repositories\TrainingRepository;
use common\repositories\UserRepository;
use common\useCases\MeetingManageCase;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class MeetingController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;
    public $userRepo;
    public $trainingRepo;
    public $meetingRepo;

    public function __construct(
        $id,
        $module,
        MeetingManageCase $useCase,
        UserRepository $userRepo,
        TrainingRepository $trainingRepo,
        MeetingRepository $meetingRepo,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->useCase = $useCase;
        $this->userRepo = $userRepo;
        $this->trainingRepo = $trainingRepo;
        $this->meetingRepo = $meetingRepo;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'OPTIONS'],
            'view' => ['GET', 'OPTIONS'],
            'start' => ['POST', 'OPTIONS'],
            'join' => ['GET', 'OPTIONS'],
            'group-create' => ['POST', 'OPTIONS'],
            'group-update' => ['PATCH', 'OPTIONS'],
            'group-delete' => ['DELETE', 'OPTIONS'],
            'group-join' => ['GET', 'OPTIONS'],
            'make-confirm' => ['POST', 'OPTIONS'],
            'check-confirm' => ['GET', 'OPTIONS'],
            'check-email' => ['GET', 'OPTIONS'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = Cors::class;
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => [Rbac::ROLE_ADMIN, Rbac::ROLE_MODERATOR, Rbac::ROLE_COACH, Rbac::ROLE_EMP],
                ],
                [
                    'allow' => true,
                    'actions' => ['join'],
                    'roles' => [Rbac::ROLE_EMP, Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                ],
                // 1 на 1 вебинар
                [
                    'allow' => true,
                    'actions' => ['start'],
                    'roles' => [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                ],
                // групповой вебинар
                [
                    'allow' => true,
                    'actions' => ['group-create', 'group-update', 'group-delete', 'check-email'],
                    'roles' => [Rbac::ROLE_ADMIN, Rbac::ROLE_MODERATOR],
                ],
                [
                    'allow' => true,
                    'actions' => ['view', 'group-join', 'make-confirm', 'check-confirm'],
                ]
            ],
        ];
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }

    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'class' => IndexAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
            ]
        );
    }

    /**
     * @OA\Get (
     *     path="/meeting/view/{meeting_uuid}",
     *     tags={"Meeting"},
     *     summary="Просмотр вебинара",
     *     @OA\Parameter (
     *           name="meeting_uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о вебинаре",
     *         @OA\JsonContent(ref="#/components/schemas/Meeting"))
     *     )
     * )
     */
    public function actionView($meeting_uuid)
    {
        $form = new MeetingViewForm();
        $form->meeting_uuid = $meeting_uuid;

        if ($this->validateBody($form)) {
            try {
                return $this->meetingRepo->get($form->meeting_uuid);
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Post (
     *     path="/meeting/start",
     *     tags={"Meeting"},
     *     summary="Создание вебинара",
     *     @OA\RequestBody(
     *         description="Параметры",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/MeetingCreateForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description = "Ссылка для подключения",
     *     ),
     * )
     */
    public function actionStart()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $form = new MeetingCreateForm();

        if ($this->validateBody($form)) {
            try {
                /** @var TrainingSession $training */
                $training = $this->trainingRepo->get($form->training_uuid);
                $this->useCase->create($form, $user);
                return $this->useCase->joinToBasicMeeting($training, $user);
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        } elseif (($training = $this->trainingRepo->get($form->training_uuid)) && $training->meeting) {
            return $this->useCase->joinToBasicMeeting($training, $user);
        }

        return $form;
    }

    /**
     * @OA\Get (
     *     path="/meeting/join/{training_uuid}",
     *     tags={"Meeting"},
     *     summary="Присоединение к вебинару",
     *     @OA\Parameter (
     *           name="training_uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description = "Ссылка для подключения",
     *     )
     * )
     */
    public function actionJoin($training_uuid)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $training = $this->trainingRepo->get($training_uuid);

        try {
            return $this->useCase->joinToBasicMeeting($training, $user);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }
    }

    /**
     * @OA\Post (
     *     path="/meeting/group-create",
     *     tags={"Meeting"},
     *     summary="Создание группового вебинара админом",
     *     @OA\RequestBody(
     *         description="Параметры",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/GroupMeetingCreateForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о вебинаре",
     *         @OA\JsonContent(ref="#/components/schemas/Meeting"))
     *     )
     * )
     */
    public function actionGroupCreate()
    {
        $this->checkAccess($this->action->id);

        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $form = new GroupMeetingCreateForm();

        if ($this->validateBody($form)) {
            try {
                /** @var Meeting $meeting */
                return $this->useCase->createGroupMeeting($form, $user);
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Patch (
     *     path="/meeting/group-update/{meeting_uuid}",
     *     tags={"Meeting"},
     *     summary="Редактирование группового вебинара админом",
     *     @OA\Parameter (
     *           name="meeting_uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\RequestBody(
     *         description="Параметры",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/GroupMeetingUpdateForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о вебинаре",
     *         @OA\JsonContent(ref="#/components/schemas/Meeting"))
     *     )
     * )
     */
    public function actionGroupUpdate($meeting_uuid)
    {
        $meeting = $this->meetingRepo->get($meeting_uuid);

        $this->checkAccess($this->action->id, $meeting);

        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $form = new GroupMeetingUpdateForm($meeting);

        if ($this->validateBody($form)) {
            try {
                /** @var Meeting $meeting */
                return $this->useCase->updateGroupMeeting($form, $user);
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Delete (
     *     path="/meeting/group-delete/{meeting_uuid}",
     *     tags={"Meeting"},
     *     summary="Удаление группового вебинара",
     *     @OA\Parameter (
     *         name="meeting_uuid",
     *         in="path",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content",
     *     )
     * )
     */
    public function actionGroupDelete($meeting_uuid)
    {
        $meeting = $this->meetingRepo->get($meeting_uuid);

        $this->checkAccess($this->action->id, $meeting);

        try {
            $this->useCase->deleteGroupMeeting($meeting);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * @OA\Get (
     *     path="/meeting/group-join/{meeting_uuid}",
     *     tags={"Meeting"},
     *     summary="Присоединение к групповому вебинару",
     *     @OA\Parameter (
     *           name="meeting_uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description = "Ссылка для подключения",
     *     )
     * )
     */
    public function actionGroupJoin($meeting_uuid)
    {
        $form = new MeetingGroupJoinForm();
        $form->meeting_uuid = $meeting_uuid;

        if ($this->validateQuery($form)) {
            try {
                /** @var Meeting $meeting */
                $meeting = $this->meetingRepo->get($form->meeting_uuid);

                $this->checkAccess($this->action->id, $meeting);

                return $this->useCase->joinToGroupMeeting($meeting, $form);
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Post (
     *     path="/meeting/make-confirm",
     *     tags={"Meeting"},
     *     summary="Подтверждение участия в групповом вебинаре",
     *     @OA\RequestBody(
     *         description="Параметры",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/MeetingMakeConfirmForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Токен",
     *     ),
     * )
     */
    public function actionMakeConfirm()
    {
        $form = new MeetingMakeConfirmForm();

        if ($this->validateBody($form)) {
            try {
                $this->useCase->makeConfirm($form);
                \Yii::$app->response->setStatusCode(200);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Get (
     *     path="/meeting/check-confirm/{token}",
     *     tags={"Meeting"},
     *     summary="Проверка подтверждения участия в групповом вебинаре",
     *     @OA\Parameter (
     *           name="token",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *          response=200,
     *         description="True or false"
     *     )
     * )
     */
    public function actionCheckConfirm($token)
    {
        $form = new MeetingCheckConfirmForm();
        $form->token = $token;

        if ($this->validateBody($form)) {
            try {
                return $this->useCase->checkConfirm($form);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Get (
     *     path="/meeting/check-email/{email}",
     *     tags={"Meeting"},
     *     summary="Проверка корректности E-mail адреса",
     *     @OA\Parameter (
     *         name="email",
     *         in="path",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description=""
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Data Validation Failed."
     *     )
     * )
     */
    public function actionCheckEmail($email)
    {
        $model = DynamicModel::validateData(compact('email'), [
            ['email', 'email'],
        ]);

        return $model;
    }

    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var Meeting $model */
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if (!empty($user)) {
            $sections = ArrayHelper::getColumn($user->sections, 'section_uuid');

            if ($user->role == Rbac::ROLE_MODERATOR) {
                if (!in_array(Section::SECTION_WEBINAR_UUID, $sections)) {
                    throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
                }
            }
        }

        if (in_array($action, ['group-update', 'group-delete'])) {
            if (!$model->isStatusCreated()) {
                throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
            }
        }

        if ($action == 'group-join') {
            if ($model->isStatusDeleted()) {
                throw new NotFoundHttpException('Вебинар не найден или был отменен.');
            }

            if (!$model->checkStartTimeToJoin()) {
                throw new ForbiddenHttpException('Вебинар еще не доступен, попробуйте войти за 10 минут до времени старта.');
            }
        }
    }
}
