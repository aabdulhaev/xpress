<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\filters\Cors;
use common\forms\AssignProgramForm;
use common\forms\AssignSubjectForm;
use common\forms\ConnectRequestForm;
use common\forms\ContactForm;
use common\forms\EmployeeUpdateForm;
use common\forms\InviteForm;
use common\forms\UserUpdateForm;
use common\models\User;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserRepository;
use common\useCases\SignupCase;
use common\useCases\UserManageCase;
use DomainException;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class EmployeeController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;
    public $signupCase;
    public $assignRepo;
    public $userRepo;

    public function __construct(
        $id,
        $module,
        UserManageCase $useCase,
        UserRepository $userRepo,
        EmployeeMentorRepository $assignRepo,
        SignupCase $signupCase,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->assignRepo = $assignRepo;
        $this->userRepo = $userRepo;
        $this->useCase = $useCase;
        $this->signupCase = $signupCase;
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
                    'actions'   => [
                        'approved',
                        'not-approved',
                        'connected',
                        'unconnected',
                        'approve-connect',
                        'decline-connect',
                        'cancel-connect',
                        'contact'
                    ],
                    'roles'     => [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                ],
                [
                    'allow' => true,
                    'actions'   => ['index','invite','change-status','program','update'],
                    'roles'     => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD', 'OPTIONS'],
            'approved' => ['GET', 'OPTIONS'],
            'not-approved' => ['GET', 'OPTIONS'],
            'connected' => ['GET', 'OPTIONS'],
            'unconnected' => ['GET', 'OPTIONS'],
        ];
    }

    /**
     * Список всех сотрудников текущего user(hr-admin)
     */
    public function actionIndex(): array
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $query = Yii::$app->request->getQueryParams();
        if (!empty($query['program'])) {
            return $this->userRepo->getEmployeeByProgram($user, $query['program'])->all();
        }

        return $user->clientEmployees;
    }

    /**
     * Список сотрудников связанных с текущим user(coach/mentor)
     */
    public function actionApproved(): array
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        return $user->approvedEmployees;
    }

    /**
     * Список сотрудников связанных с текущим user(coach/mentor)
     */
    public function actionNotApproved(): array
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        return $user->notApprovedEmployees;
    }

    /**
     * Список сотрудников связанных с текущим user(coach/mentor)
     */
    public function actionConnected(): array
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        return $user->connectedEmployees;
    }

    /**
     * Список сотрудников запросивших доступ к текущему user(coach/mentor)
     */
    public function actionUnconnected(): array
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        return $user->unconnectedEmployees;
    }

    /**
     * Одобрение заявки текущим user(coach/mentor)
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionApproveConnect($user_uuid): ConnectRequestForm
    {
        /**
         * @var $mentor User
         */
        $mentor = Yii::$app->user->identity;
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new ConnectRequestForm($employee, $mentor);
        $form->scenario = 'mentor';
        // TODO добавить в форму поле указывающее на обязательный комментарий
        if (empty($form->comment)) {
            $form->comment = Yii::$app->request->getBodyParam(
                'comment',
                Html::encode($mentor->fullName) . '  approved the employee'
            );
        }

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
     * Отключение сотрудника текущим user(coach/mentor)
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDeclineConnect($user_uuid): ConnectRequestForm
    {
        /**
         * @var $mentor User
         */
        $mentor = Yii::$app->user->identity;
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new ConnectRequestForm($employee, $mentor);
        $form->scenario = 'mentor';
        // TODO добавить в форму поле указывающее на обязательный комментарий
        if (empty($form->comment)) {
            $form->comment = Yii::$app->request->getBodyParam(
                'comment',
                Html::encode($mentor->fullName) . '  approved the employee'
            );
        }

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
     * Отклонение заявки текущим user(coach/mentor)
     * @param $user_uuid
     * @return ConnectRequestForm
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionCancelConnect($user_uuid): ConnectRequestForm
    {
        /**
         * @var $mentor User
         */
        $mentor = Yii::$app->user->identity;
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new ConnectRequestForm($employee, $mentor);
        $form->scenario = 'mentor';

        if ($this->validateBody($form)) {
            try {
                $this->useCase->cancelConnect($form);
                Yii::$app->response->setStatusCode(200);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
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
        /**
         * @var $userFrom User
         */
        $userFrom = Yii::$app->user->identity;
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
     * Изменение статуса сотрудника текущего user(hr-admin)
     * @param $user_uuid
     * @return EmployeeUpdateForm|User
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionChangeStatus($user_uuid)
    {
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new EmployeeUpdateForm($employee);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->editStatus($form);
                Yii::$app->response->setStatusCode(200);
                $employee->refresh();
                return $employee;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @param $user_uuid
     * @return UserUpdateForm|\yii\db\ActiveRecord
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($user_uuid)
    {
        /** @var User $employee */
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new UserUpdateForm($employee);
        $form->authUser = \Yii::$app->user->identity;

        if ($this->validateBody($form)) {
            try {
                $this->signupCase->assignSubjects($form->user, $form->subjects);
                $this->signupCase->assignCompetencies($form->user, $form->competencies);
                $this->useCase->manageUserPrograms($form);

                $this->useCase->edit($form);
                $this->useCase->saveCompetencyProfile($form);

                Yii::$app->response->setStatusCode(200);
                $employee->refresh();
                return $employee;
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
        /**
         * @var $userFrom User
         */
        $userFrom = Yii::$app->user->identity;
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
     * Редактирование программ сотрудника текущим user(hr-admin)
     * @param $user_uuid
     * @return AssignProgramForm|User
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionProgram($user_uuid)
    {
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new AssignProgramForm($employee);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->assignPrograms($employee->user_uuid, $form);
                Yii::$app->response->setStatusCode(200);
                $employee->refresh();
                return $employee;
            } catch (DomainException $e) {
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
     */
    public function checkAccess(string $action, $model = null): void
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;


        if ((in_array($action, ['change-status','program','invite'])) && $model->client_uuid !== $user->client_uuid) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if (in_array($action, ['connected','requesting','approve-connect','decline-connect','contact'])) {
            if (!$user->getEmployees()->andWhere(['user_uuid' => $model->user_uuid])->exists()) {
                throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
            }
        }
    }
}
