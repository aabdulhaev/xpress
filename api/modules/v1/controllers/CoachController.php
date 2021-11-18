<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\actions\coach\IndexAction;
use common\access\Rbac;
use common\forms\AssignCoachForm;
use common\forms\ConnectRequestForm;
use common\forms\ContactForm;
use common\forms\CreateConnectForm;
use common\forms\CreateConnectRequestForm;
use common\forms\InviteForm;
use common\models\ClientCoach;
use common\models\EmployeeMentor;
use common\models\User;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserRepository;
use common\useCases\ClientCase;
use common\useCases\UserManageCase;
use DomainException;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class CoachController extends ActiveController
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;
    public $clientCase;
    public $assignRepo;
    public $userRepo;

    public function __construct(
        $id,
        $module,
        UserManageCase $useCase,
        UserRepository $userRepo,
        EmployeeMentorRepository $assignRepo,
        ClientCase $clientCase,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->assignRepo = $assignRepo;
        $this->userRepo = $userRepo;
        $this->useCase = $useCase;
        $this->clientCase = $clientCase;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [Rbac::ROLE_ADMIN],
                ],
                [
                    'allow' => true,
                    'actions' => [
                        'approved',
                        'not-approved',
                        'connected',
                        'unconnected',
                        'approve-connect',
                        'decline-connect',
                        'create-request',
                        'contact'
                    ],
                    'roles' => [Rbac::ROLE_EMP],
                ],
                [
                    'allow' => true,
                    'actions' => [
                        'index',
                        'connect',
                        'newest',
                        'add',
                        'remove',
                        'invite',
                        'change-status',
                        'program'
                    ],
                    'roles' => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        return [
            'index' => ['GET', 'HEAD', 'OPTIONS'],
            'approved' => ['GET', 'OPTIONS'],
            'not-approved' => ['GET', 'OPTIONS'],
            'connected' => ['GET', 'OPTIONS'],
            'unconnected' => ['GET', 'OPTIONS'],
            'approve-connect' => ['PATCH', 'OPTIONS'],
            'decline-connect' => ['PATCH', 'OPTIONS'],
            'create-request' => ['POST', 'OPTIONS'],
            'contact' => ['POST', 'OPTIONS'],
            'newest' => ['GET', 'OPTIONS'],
            'add' => ['POST', 'OPTIONS'],
            'remove' => ['POST', 'OPTIONS'],
            'invite' => ['POST', 'OPTIONS'],
            'connect' => ['POST', 'OPTIONS'],
        ];
    }

    public function actions(): array
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'class' => IndexAction::class,
                    'modelClass' => $this->modelClass
                ],
                'view' => false,
                'create' => false,
                'update' => false,
                'delete' => false
            ]
        );
    }

    /**
     * Список коучей связанных и подтверждённых текущим user(employee)
     *
     * @return ActiveDataProvider
     */
    public function actionApproved(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getApprovedCoaches();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Список коучей связанных но не подтверждённых текущим user(employee)
     *
     * @return ActiveDataProvider
     */
    public function actionNotApproved(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getNotApprovedCoaches();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Список коучей связанных с текущим user(employee)
     *
     * @return ActiveDataProvider
     */
    public function actionConnected(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getConnectedCoaches();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Список доступных коучей текущему user(employee)
     *
     * @return ActiveDataProvider
     */
    public function actionUnconnected(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getUnconnectedCoaches();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Отклонение заявки текущим user(employee)
     *
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDeclineConnect($user_uuid): ConnectRequestForm
    {
        /** @var User $employee */
        $employee = Yii::$app->user->identity;
        /** @var User $coach */
        $coach = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $coach);

        $form = new ConnectRequestForm($employee, $coach);
        $form->scenario = 'employee';

        if ($this->validateBody($form)) {
            try {
                $this->useCase->declineConnect($form);
                Yii::$app->response->setStatusCode(200);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Одобрение заявки текущим user(employee)
     *
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionApproveConnect($user_uuid): ConnectRequestForm
    {
        /** @var User $employee */
        $employee = Yii::$app->user->identity;
        /** @var User $coach */
        $coach = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $coach);

        $form = new ConnectRequestForm($employee, $coach);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->approveConnect($form);
                Yii::$app->response->setStatusCode(200);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * @param $user_uuid
     * @return ConnectRequestForm|CreateConnectRequestForm|EmployeeMentor
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionCreateRequest($user_uuid)
    {
        /** @var User $employee */
        $employee = Yii::$app->user->identity;
        /** @var User $coach */
        $coach = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $coach);

        $form = new CreateConnectRequestForm($employee, $coach);

        if ($this->validateBody($form)) {
            try {
                $connect = $this->useCase->createConnect($form);
                Yii::$app->response->setStatusCode(201);
                return $connect;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        if ($form->getErrors('employee_uuid') && $form->getErrors('mentor_uuid')) {
            $requestForm = new ConnectRequestForm($employee, $coach);
            $requestForm->comment = $form->comment;

            if ($requestForm->validate()) {
                try {
                    $this->useCase->requestConnect($requestForm);
                    Yii::$app->response->setStatusCode(201);
                    return $requestForm;
                } catch (DomainException $e) {
                    throw new BadRequestHttpException($e->getMessage(), null, $e);
                }
            }
        }

        return $form;
    }

    /**
     * Отправка сообщения сотруднику текущим user(coach/mentor)
     *
     * @param $user_uuid
     * @return ContactForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionContact($user_uuid): ContactForm
    {
        /** @var User $userFrom */
        $userFrom = Yii::$app->user->identity;
        /** @var User $userTo */
        $userTo = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $userTo);

        $form = new ContactForm($userFrom, $userTo);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->contact($form);
                Yii::$app->response->setStatusCode(201);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * Список всех коучей НЕ добавленных в компанию текущего пользователя (hr)
     *
     * @return ActiveDataProvider
     */
    public function actionNewest(): ActiveDataProvider
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $subQuery = $user->getClientCoaches()->select('user_uuid');
        $coachesQuery = $this->userRepo->getByRole(Rbac::ROLE_COACH)
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->andWhere(['NOT IN', 'user_uuid', $subQuery]);

        return $this->userRepo->getProvider($coachesQuery);
    }

    /**
     * Выбрать нового коуча для компании
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionAdd(): array
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $coaches_uuid = Yii::$app->request->getBodyParam('coaches_uuid');
        $errors = [];
        $forms = [];

        foreach ($coaches_uuid as $coach_uuid) {
            $forms[] = new AssignCoachForm(['client_uuid' => $user->client_uuid, 'coach_uuid' => $coach_uuid]);
        }

        $rows = [];
        Model::validateMultiple($forms);

        foreach ($forms as $form) {
            if (!$form->hasErrors()) {
                $rows[] = [
                    $form->client_uuid,
                    $form->coach_uuid,
                    ClientCoach::STATUS_APPROVED,
                    time(),
                    Yii::$app->user->id
                ];
                Yii::$app->response->setStatusCode(201);
                continue;
            }
            $errors[] = $form->getErrors();
        }
        $inserted = $this->useCase->batchAddClientCoach($rows);

        return ['created' => $inserted, 'errors' => $errors];
    }

    /**
     *  Отключение коучей от компании
     * @throws \yii\db\Exception
     */
    public function actionRemove()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $coaches_uuid = Yii::$app->request->getBodyParam('coaches_uuid');
        $deleted = $this->clientCase->unsetCoaches($coaches_uuid, $user->client_uuid);

        if ($deleted) {
            Yii::$app->response->setStatusCode(204);
        }
    }

    /**
     * Создание и отправка приглашения текущим user(hr-admin)
     *
     * @param $user_uuid
     * @return InviteForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionInvite($user_uuid): InviteForm
    {
        /** @var User $userFrom */
        $userFrom = Yii::$app->user->identity;
        /** @var User $userTo */
        $userTo = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $userTo);

        $form = new InviteForm($userFrom, $userTo);
        $form->load(Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->invite($form);
                Yii::$app->response->setStatusCode(201);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * Создание связей коуч-сотрудник
     *
     * @return array
     * @throws Exception
     */
    public function actionConnect(): array
    {
        $coaches_uuid = Yii::$app->request->getBodyParam('coaches_uuid');
        $employees_uuid = Yii::$app->request->getBodyParam('employees_uuid');
        $coaches = $this->userRepo->getByUuid($coaches_uuid)->all();
        $employees = $this->userRepo->getByUuid($employees_uuid)->all();
        $errors = [];
        /** @var User $coach */
        foreach ($coaches as $idx => $coach) {
            try {
                $this->checkAccess($this->action->id, $coach);
            } catch (ForbiddenHttpException $exception) {
                $errors[] = [
                    'coach_uuid' => [
                        $coach->user_uuid => "Вы не можете выполнять данное действие с пользователем"
                    ]
                ];
                unset($coaches[$idx]);
            }
        }
        /** @var User $employee */
        foreach ($employees as $idx => $employee) {
            try {
                $this->checkAccess('em-check-connect', $employee);
            } catch (ForbiddenHttpException $exception) {
                $errors[] = [
                    'employee_uuid' => [
                        $employee->user_uuid => "Вы не можете выполнять данное действие с пользователем"
                    ]
                ];
                unset($employees[$idx]);
            }
        }
        $relations = array_fill_keys(
            ArrayHelper::getColumn($employees, 'user_uuid'),
            ArrayHelper::getColumn($coaches, 'user_uuid')
        );
        $forms = [];
        foreach ($relations as $empUuid => $coachUuids) {
            foreach ($coachUuids as $coachUuid) {
                $forms[] = new CreateConnectForm(['employee_uuid' => $empUuid, 'mentor_uuid' => $coachUuid]);
            }
        }
        $rows = [];
        Model::validateMultiple($forms);
        foreach ($forms as $form) {
            if (!$form->hasErrors()) {
                $rows[] = [
                    $form->mentor_uuid,
                    $form->employee_uuid,
                    count($forms) > 1 ? EmployeeMentor::STATUS_NOT_APPROVED : EmployeeMentor::STATUS_APPROVED,
                    time(),
                    Yii::$app->user->id
                ];
                Yii::$app->response->setStatusCode(201);
            } else {
                $errors[] = $form->getErrorSummary(0);
            }
        }

        list($inserted, $updated) = $this->useCase->batchCreateActiveConnect(
            $rows,
            $errors,
            $employees,
            $coaches
        );

        return ['created' => $inserted, 'updated' => $updated, 'errors' => $errors];
    }

    /**
     * Проверяем доступ к действиям
     *
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (
            $action == 'create-request' &&
            !$user->getConnectedCoaches()->andWhere(['user_uuid' => $model->user_uuid])->exists()
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if (
            in_array($action, ['approve-connect', 'decline-connect']) &&
            !$user->getApprovedCoaches()
                ->andWhere(['user_uuid' => $model->user_uuid])
                ->exists()
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if (
            $action == 'contact' &&
            (!$this->assignRepo->isConnectedMentor($user, $model->user_uuid))
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if (
            in_array($action, ['invite', 'connect']) &&
            !$user->getClientCoaches()
                ->andWhere(['user_uuid' => $model->user_uuid])
                ->exists()
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if ($action === 'em-check-connect' && $user->client_uuid !== $model->client_uuid) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }
    }
}
