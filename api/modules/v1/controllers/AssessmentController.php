<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\filters\Cors;
use common\forms\AssignProgramForm;
use common\forms\ConnectRequestForm;
use common\forms\ContactForm;
use common\forms\EmployeeUpdateForm;
use common\forms\InviteForm;
use common\models\User;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserRepository;
use common\useCases\UserManageCase;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class AssessmentController extends Controller
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
        $behaviors['corsFilter'] = Cors::class;
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions'   => ['connected','requesting','approve-connect','decline-connect','contact'],
                    'roles'     => [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                ],
                [
                    'allow' => true,
                    'actions'   => ['index','invite','change-status','program'],
                    'roles'     => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * Список сотрудников связанных с текущим user(coach/mentor)
     */
    public function actionConnected()
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;
        return $user->activeEmployees;
    }

    /**
     * Список сотрудников запросивших доступ к текущему user(coach/mentor)
     */
    public function actionRequesting()
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;
        return $user->draftEmployees;
    }

    /**
     * Одобрение заявки текущим user(coach/mentor)
     */
    public function actionApproveConnect($user_uuid)
    {
        /**
         * @var $mentor User
         */
        $mentor = \Yii::$app->user->identity;
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new ConnectRequestForm($employee, $mentor);

        $form->load(\Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->approveConnect($form);
                \Yii::$app->response->setStatusCode(200);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Отклонение заявки текущим user(coach/mentor)
     */
    public function actionDeclineConnect($user_uuid)
    {

        /**
         * @var $mentor User
         */
        $mentor = \Yii::$app->user->identity;
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new ConnectRequestForm($employee, $mentor);
        $form->load(\Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->declineConnect($form);
                \Yii::$app->response->setStatusCode(200);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Отправка сообщения сотруднику текущим user(coach/mentor)
     */
    public function actionContact($user_uuid)
    {
        /**
         * @var $userFrom User
         */
        $userFrom = \Yii::$app->user->identity;
        $userTo = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $userTo);

        $form = new ContactForm($userFrom, $userTo);
        $form->load(\Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->contact($form);
                \Yii::$app->response->setStatusCode(201);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }


    /**
     * Список всех сотрудников текущего user(hr-admin)
     */
    public function actionIndex()
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;
        return $user->hrEmployees;
    }

    /**
     * Изменение статуса сотрудника текущего user(hr-admin)
     */
    public function actionChangeStatus($user_uuid)
    {
        /**
         * @var $user User
         */

        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new EmployeeUpdateForm($employee);
        $form->load(\Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->editStatus($form);
                \Yii::$app->response->setStatusCode(200);
                $employee->refresh();
                return $employee;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }


    /**
     * Создание и отправка приглашения текущим user(hr-admin)
     */
    public function actionInvite($user_uuid)
    {
        /**
         * @var $userFrom User
         */
        $userFrom = \Yii::$app->user->identity;
        $userTo = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $userTo);

        $form = new InviteForm($userFrom, $userTo);
        $form->load(\Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->invite($form);
                \Yii::$app->response->setStatusCode(201);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * Редактирование программ сотрудника текущим user(hr-admin)
     */
    public function actionProgram($user_uuid)
    {
        $employee = $this->userRepo->get($user_uuid);
        $this->checkAccess($this->action->id, $employee);

        $form = new AssignProgramForm($employee);
        $form->load(\Yii::$app->request->getBodyParams(), '');

        if ($form->validate()) {
            try {
                $this->useCase->assignPrograms($employee->user_uuid, $form);
                \Yii::$app->response->setStatusCode(200);
                $employee->refresh();
                return $employee;
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
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        if (
            (in_array($action, ['change-status','program','invite','contact'])) &&
            $model->client_uuid !== $user->client_uuid
        ) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if (in_array($action, ['connected','requesting','approve-request','decline-request','contact'])) {
            if (!$user->getEmployees()->andWhere(['user_uuid' => $model->user_uuid])->exists()) {
                throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
            }
        }
    }
}
