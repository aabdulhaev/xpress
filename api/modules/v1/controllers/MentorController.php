<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\forms\ConnectRequestForm;
use common\forms\ContactForm;
use common\forms\CreateConnectForm;
use common\forms\CreateConnectRequestForm;
use common\forms\InviteForm;
use common\forms\MentorUpdateForm;
use common\models\EmployeeMentor;
use common\models\User;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserRepository;
use common\useCases\UserManageCase;
use DomainException;
use Exception;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class MentorController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;
    public $assignRepo;
    public $userRepo;

    public function __construct(
        $id,
        $module,
        UserManageCase $useCase,
        UserRepository $userRepo,
        EmployeeMentorRepository $assignRepo,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->assignRepo = $assignRepo;
        $this->userRepo = $userRepo;
        $this->useCase = $useCase;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions'   => [
                        'approved',
                        'not-approved',
                        'connected',
                        'unconnected',
                        'create-request',
                        'contact',
                        'approve-connect',
                        'decline-connect'
                    ],
                    'roles'     => [Rbac::ROLE_EMP],
                ],
                [
                    'allow' => true,
                    'actions'   => ['index','change-status','invite','connect'],
                    'roles'     => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        return [
            'approved' => ['GET', 'OPTIONS'],
            'not-approved' => ['GET', 'OPTIONS'],
            'connected' => ['GET', 'OPTIONS'],
            'unconnected' => ['GET', 'OPTIONS'],
            'index' => ['GET', 'OPTIONS'],
            'create-request' => ['POST', 'OPTIONS'],
            'decline-connect' => ['PATCH', 'OPTIONS'],
            'contact' => ['POST', 'OPTIONS'],
            'invite' => ['POST', 'OPTIONS'],
            'change-status' => ['POST', 'OPTIONS'],
        ];
    }

    /**
     * Список менторов связанных с текущим user(employee)
     */
    public function actionApproved(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getApprovedMentors();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Список не подтверждённых менторов компании текущего user(employee)
     */
    public function actionNotApproved(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getNotApprovedMentors();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Список менторов связанных с текущим user(employee)
     */
    public function actionConnected(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getConnectedMentors();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Список менторов компании не подключенных к текущему user(employee)
     */
    public function actionUnconnected(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getUnconnectedMentors();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Отклонение заявки текущим user(employee)
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDeclineConnect($user_uuid): ConnectRequestForm
    {
        /** @var User $employee */
        $employee = Yii::$app->user->identity;
        /** @var User $mentor */
        $mentor = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $mentor);

        $form = new ConnectRequestForm($employee, $mentor);
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
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionApproveConnect($user_uuid): ConnectRequestForm
    {
        /** @var User $employee */
        $employee = Yii::$app->user->identity;
        /** @var User $mentor */
        $mentor = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $mentor);

        $form = new ConnectRequestForm($employee, $mentor);

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
     * Создание запроса на подключение к ментору текущим user(employee)
     *
     * @param $user_uuid
     * @return ConnectRequestForm|CreateConnectRequestForm|EmployeeMentor
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionCreateRequest($user_uuid)
    {
        /** @var User $employee */
        $employee = Yii::$app->user->identity;
        /** @var User $mentor */
        $mentor = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $mentor);

        /** @var CreateConnectRequestForm $form */
        $form = new CreateConnectRequestForm($employee, $mentor);

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
            /** @var ConnectRequestForm $requestForm */
            $requestForm = new ConnectRequestForm($employee, $mentor);
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
     * Список всех менторов текущего user(hr-admin)
     */
    public function actionIndex(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = $user->getClientMentors();
        return $this->userRepo->getProvider($query);
    }

    /**
     * Изменение статуса сотрудника текущего user(hr-admin)
     * @param $user_uuid
     * @return MentorUpdateForm|User
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionChangeStatus($user_uuid)
    {
        /** @var User $mentor */
        $mentor = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $mentor);

        $form = new MentorUpdateForm($mentor);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->editStatus($form);
                Yii::$app->response->setStatusCode(200);
                $mentor->refresh();
                return $mentor;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }


    /**
     * Создание и отправка приглашения текущим user(hr-admin)
     * @param $user_uuid
     * @return InviteForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionInvite($user_uuid): InviteForm
    {
        /** @var User $userFrom */
        $userFrom = Yii::$app->user->identity;
        /** @var User $userTo */
        $userTo = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $userTo);

        $form = new InviteForm($userFrom, $userTo);

        if ($this->validateBody($form)) {
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
     * @throws \yii\db\Exception
     */
    public function actionConnect(): array
    {
        $mentors_uuid = Yii::$app->request->getBodyParam('mentors_uuid');
        $employees_uuid = Yii::$app->request->getBodyParam('employees_uuid');
        $mentors = $this->userRepo->getByUuid($mentors_uuid)->all();
        $employees = $this->userRepo->getByUuid($employees_uuid)->all();
        $errors = [];
        /** @var User $mentor */
        foreach ($mentors as $idx => $mentor) {
            try {
                $this->checkAccess($this->action->id, $mentor);
            } catch (ForbiddenHttpException $exception) {
                $errors[] = [
                    'mentor_uuid' => [
                        $mentor->user_uuid => "Вы не можете выполнять данное действие с пользователем"
                    ]
                ];
                unset($mentors[$idx]);
            }
        }

        /** @var User $employee */
        foreach ($employees as $idx => $employee) {
            try {
                $this->checkAccess($this->action->id, $employee);
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
            ArrayHelper::getColumn($mentors, 'user_uuid')
        );
        $forms = [];
        foreach ($relations as $empUuid => $mentorUuids) {
            foreach ($mentorUuids as $mentorUuid) {
                $forms[] = new CreateConnectForm(['employee_uuid' => $empUuid,'mentor_uuid' => $mentorUuid]);
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
                continue;
            }
            $errors[] = $form->getErrorSummary(0);
        }

        list($inserted, $updated) = $this->useCase->batchCreateActiveConnect(
            $rows,
            $errors,
            $employees,
            $mentors
        );

        return ['created' => $inserted, 'updated' => $updated, 'errors' => $errors];
    }


    /**
     * Проверяем доступ к действиям
     * @param string $action
     * @param null $model
     * @throws ForbiddenHttpException
     */
    public function checkAccess(string $action, $model = null): void
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (
            (in_array($action, ['create-request'])) &&
            (
                $model->client_uuid !== $user->client_uuid ||
                $this->assignRepo->isConnectedMentor($user, $model->user_uuid)
            )
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие. 1');
        }

        if (
            (in_array($action, ['contact'])) &&
            (
                $model->client_uuid !== $user->client_uuid ||
                !$this->assignRepo->isConnectedMentor($user, $model->user_uuid)
            )
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие. 2');
        }

        if (
            (in_array($action, ['change-status','invite','connect'])) &&
            $model->client_uuid !== $user->client_uuid
        ) {
            throw new ForbiddenHttpException(
                'Вы не можете выполнять данное действие. 3' .
                $model->client_uuid .
                ' - ' .
                $user->client_uuid
            );
        }

        if (in_array($action, ['approve-connect','decline-connect'])) {
            if (!$user->getClientMentors()->andWhere(['user_uuid' => $model->user_uuid])->exists()) {
                throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
            }
        }
    }
}
