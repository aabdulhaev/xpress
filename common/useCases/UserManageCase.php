<?php

declare(strict_types=1);

namespace common\useCases;

use common\access\Rbac;
use common\components\Google;
use common\dispatchers\DeferredEventDispatcher;
use common\forms\AssignProgramForm;
use common\forms\AssignSubjectForm;
use common\forms\BaseUserUpdateForm;
use common\forms\ConnectRequestForm;
use common\forms\ContactForm;
use common\forms\CreateConnectForm;
use common\forms\CreateConnectRequestForm;
use common\forms\InviteForm;
use common\forms\UserCreateForm;
use common\forms\UserUpdateForm;
use common\models\ClientCoach;
use common\models\EmployeeMentor;
use common\models\events\UserConnect;
use common\models\events\UserManyConnections;
use common\models\Program;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserCompetencyProfile;
use common\models\UserProgram;
use common\models\UserTraining;
use common\repositories\ClientRepository;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserProgramRepository;
use common\repositories\UserRepository;
use common\services\TransactionManager;
use DateTime;
use DomainException;
use Yii;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class UserManageCase
{

    public $repo;
    public $userRepo;
    public $trainingRepo;
    public $clientRepo;
    public $userProgramRepo;
    private $transaction;

    private $dispatcher;

    public function __construct(
        EmployeeMentorRepository $repo,
        UserRepository $userRepo,
        TransactionManager $transaction,
        DeferredEventDispatcher $dispatcher,
        TrainingSession $trainingRepo,
        ClientRepository $clientRepo,
        UserProgramRepository $userProgramRepo
    ) {
        $this->repo = $repo;
        $this->userRepo = $userRepo;
        $this->transaction = $transaction;
        $this->trainingRepo = $trainingRepo;
        $this->dispatcher = $dispatcher;
        $this->clientRepo = $clientRepo;
        $this->userProgramRepo = $userProgramRepo;
    }

    public function createActiveConnect(CreateConnectForm $form): EmployeeMentor
    {
        $connect = EmployeeMentor::create($form->employee_uuid, $form->mentor_uuid);
        $connect->status = EmployeeMentor::STATUS_APPROVED;
        $this->repo->save($connect);
        return $connect;
    }

    /**
     * @throws Exception
     */
    public function batchAddClientCoach(array $rows): int
    {
        return Yii::$app->db->createCommand()
            ->batchInsert(
                ClientCoach::tableName(),
                [
                    'client_uuid',
                    'coach_uuid',
                    'status',
                    'created_at',
                    'created_by',
                ],
                $rows
            )
            ->execute();
    }

    /**
     * @throws Exception
     */
    public function batchCreateActiveConnect(array $rows, array &$errors, array $employees, array $coaches): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $inserted = [];
            Yii::$app->db->createCommand()
                ->batchInsert(
                    EmployeeMentor::tableName(),
                    [
                        'mentor_uuid',
                        'employee_uuid',
                        'status',
                        'created_at',
                        'created_by',
                    ],
                    $rows
                )
                ->execute();
            foreach ($rows as $row) {
                $inserted[] = [
                    'employee_uuid' => $row[1],
                    'mentor_uuid' => $row[0]
                ];
            }

            $updatedRows = [];
            $updated = [];
            foreach ($errors as $key => $error) {
                if ($error['not_unique']) {
                    $updatedRows[$error['employee_uuid']][] = $error['mentor_uuid'];
                    unset($errors[$key]);
                }
            }
            foreach ($updatedRows as $employeeUuid => $mentorsUuid) {
                EmployeeMentor::updateAll(
                    [
                        'status' => count($mentorsUuid) > 1
                            ? EmployeeMentor::STATUS_NOT_APPROVED
                            : EmployeeMentor::STATUS_APPROVED
                    ],
                    [
                        'and',
                        ['employee_uuid' => $employeeUuid],
                        [
                            'in',
                            'mentor_uuid',
                            $mentorsUuid
                        ]
                    ]
                );
                $updated[] = [
                    'employee_uuid' => $employeeUuid,
                    'mentors_uuid' => $mentorsUuid
                ];
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->dispatcher->defer();
        /** @var User $userFrom */
        $userFrom = $this->userRepo->get(Yii::$app->user->id);
        foreach ($employees as $employee) {
            if (count($coaches) > 1) {
                $this->dispatcher->dispatch(
                    new UserManyConnections($userFrom, $employee, end($coaches))
                );
            } else {
                foreach ($coaches as $coach) {
                    $this->dispatcher->dispatch(
                        new UserConnect($userFrom, $employee, $coach)
                    );
                }
            }
        }
        $this->dispatcher->release();

        return [$inserted, $updated];
    }

    public function createConnect(CreateConnectRequestForm $form): EmployeeMentor
    {
        $connect = EmployeeMentor::create(
            $form->employee_uuid,
            $form->mentor_uuid,
            $form->comment
        );
        Yii::info($connect->attributes, __METHOD__);
        $this->repo->save($connect);
        return $connect;
    }

    public function requestConnect(ConnectRequestForm $form)
    {
        $connect = $this->repo->get($form->employee->user_uuid, $form->mentor->user_uuid);
        $connect->comment = $form->comment;
        $connect->status = EmployeeMentor::STATUS_NOT_APPROVED;
        $connect->request();
        $this->repo->save($connect);
    }

    public function approveConnect(ConnectRequestForm $form): void
    {
        $connect = $this->repo->get($form->employee->user_uuid, $form->mentor->user_uuid);
        $connect->approve();
        $this->repo->save($connect);
    }

    public function declineConnect(ConnectRequestForm $form): void
    {
        $connect = $this->repo->get($form->employee->user_uuid, $form->mentor->user_uuid);
        Yii::debug($connect);
        $connect->decline($form->comment, $form->scenario);
        $this->repo->save($connect);
    }

    public function cancelConnect(ConnectRequestForm $form): void
    {
        $connect = $this->repo->get($form->employee->user_uuid, $form->mentor->user_uuid);
        $connect->cancel($form->comment);
        $this->repo->remove($connect);
    }

    /**
     * @throws \Exception
     */
    public function editStatus(BaseUserUpdateForm $form): void
    {
        $user = $form->user;

        if (in_array($user->role, [Rbac::ROLE_MENTOR, Rbac::ROLE_COACH])) {
            $employeeAssignments = $user->employeeAssignments;
            $trainingAssignments = $user->trainingAssignments;
            $trainings = $user->trainings;
            if ($form->status === User::STATUS_SUSPENDED) {
                foreach ($employeeAssignments as $employeeAssignment) {
                    $employeeAssignment->status = EmployeeMentor::STATUS_UNCONNECTED;
                }
                $user->employeeAssignments = $employeeAssignments;

                $deleteTrainings = [];
                foreach ($trainings as $training) {
                    $startDate = new DateTime($training->start_at_tc);
                    $nowDate = new DateTime();
                    $nearest = $nowDate < $startDate;

                    if ($nearest) {
                        $deleteTrainings[] = $training->training_uuid;
                        $training->status = TrainingSession::STATUS_DELETED;
                    }
                }
                $user->trainings = $trainings;

                foreach ($trainingAssignments as $trainingAssignment) {
                    if (in_array($trainingAssignment->training_uuid, $deleteTrainings)) {
                        $trainingAssignment->status = UserTraining::STATUS_DELETED;
                    }
                }
                $user->trainingAssignments = $trainingAssignments;

                if ($user->role === Rbac::ROLE_COACH) {
                    $clientCoachesAssignments = $user->clientCoachesAssignments;
                    foreach ($clientCoachesAssignments as $clientCoachesAssignment) {
                        $clientCoachesAssignment->status = ClientCoach::STATUS_DRAFT;
                    }
                    $user->clientCoachesAssignments = $clientCoachesAssignments;
                }
            } else {
                foreach ($employeeAssignments as $employeeAssignment) {
                    if ($employeeAssignment->status === EmployeeMentor::STATUS_UNCONNECTED) {
                        $employeeAssignment->status = EmployeeMentor::STATUS_NOT_APPROVED;
                    }
                }
                $user->employeeAssignments = $employeeAssignments;

                $nowDate = new DateTime();
                $notConfirmTrainings = [];
                foreach ($trainingAssignments as $trainingAssignment) {
                    if ($trainingAssignment->status === UserTraining::STATUS_DELETED) {
                        $trainingAssignment->status = UserTraining::STATUS_NOT_CONFIRM;
                        $notConfirmTrainings[] = $trainingAssignment->training_uuid;
                    }
                }
                $user->trainingAssignments = $trainingAssignments;

                foreach ($trainings as $training) {
                    $startDate = new DateTime($training->start_at_tc);
                    $nearest = $nowDate < $startDate;
                    if (
                        $training->status === TrainingSession::STATUS_DELETED &&
                        $nearest
                    ) {
                        if (in_array($training->training_uuid, $notConfirmTrainings)) {
                            $training->status = TrainingSession::STATUS_NOT_CONFIRM;
                        } else {
                            $training->status = TrainingSession::STATUS_FREE;
                        }
                    }
                }
                $user->trainings = $trainings;

                if ($user->role === Rbac::ROLE_COACH) {
                    $clientCoachesAssignments = $user->clientCoachesAssignments;
                    foreach ($clientCoachesAssignments as $clientCoachesAssignment) {
                        if ($clientCoachesAssignment->status === ClientCoach::STATUS_DRAFT) {
                            $clientCoachesAssignment->status = ClientCoach::STATUS_APPROVED;
                        }
                    }
                    $user->clientCoachesAssignments = $clientCoachesAssignments;
                }
            }
        }

        $user->editStatus($form->status);
        $this->userRepo->save($user);
    }

    public function editProgram(BaseUserUpdateForm $form): void
    {
        $user = $form->user;
        $user->editStatus($form->status);
        $this->userRepo->save($user);
    }

    /**
     * @throws \Exception
     */
    public function assignPrograms(string $user_uuid, AssignProgramForm $form): void
    {
        $user = $this->userRepo->get($user_uuid);

        $this->transaction->wrap(function () use ($user, $form) {
            $user->revokePrograms();
            $this->userRepo->save($user);
            foreach ($form->programs as $program => $isOn) {
                if ($isOn) {
                    $user->assignProgram($program);
                }
            }

            if (!$user->getPrograms()->exists()) {
                $user->editStatus(User::STATUS_SUSPENDED);
            }

            $this->userRepo->save($user);
        });
    }

    public function contact(ContactForm $form): void
    {
        $form->userFrom->contact($form->userTo, $form->body);
        $this->userRepo->dispatch($form->userFrom);
    }

    public function invite(InviteForm $form): void
    {
        $form->userFrom->invite($form->userTo, $form->body);
        $this->userRepo->dispatch($form->userFrom);
    }

    /**
     * @param UserUpdateForm $form
     */
    public function edit(UserUpdateForm $form): void
    {
        $model = $form->user;
        $model->edit($form);
        $this->userRepo->save($model);
    }

    public function assignSubjects($user_uuid, AssignSubjectForm $form)
    {
        $profile = new ProfileManageCase($this->userRepo, $this->transaction);
        $profile->assignSubjects($user_uuid, $form);
    }

    /**
     * @param $form UserCreateForm|UserUpdateForm
     */
    public function saveCompetencyProfile($form)
    {
        if ($form->image) {
            $model = new UserCompetencyProfile();
            $model->image = $form->image;
            $model->link('owner', $form->user);
            if (!$model->save()) {
                Yii::error($model->getErrorSummary(true));
            }
        }
    }

    /**
     * Администратор может назначать и снимать программы с пользователей
     *
     * @param $form UserUpdateForm
     * @throws \Exception
     */
    public function manageUserPrograms(UserUpdateForm $form)
    {
        $this->transaction->wrap(function () use ($form) {
            if (is_array($form->programs) && !empty($form->programs)) {
                foreach ($form->programs as $program) {
                    $program_uuid = ArrayHelper::getValue($program, 'program_uuid', '');
                    $enable = ArrayHelper::getValue($program, 'enable', false);
                    $session = intval(ArrayHelper::getValue($program, 'session', 0));

                    /** @var UserProgram $userProgramAssignment */
                    $userProgramAssignment = null;
                    if ($form->user->programAssignments) {
                        foreach ($form->user->programAssignments as $programAssignment) {
                            if ($programAssignment->program_uuid === $program_uuid) {
                                $userProgramAssignment = $programAssignment;
                            }
                        }
                    }

                    /** if program is on */
                    if ($enable == 'true') {
                        $dynamicModel = DynamicModel::validateData(compact('session'), [
                            [['session'], 'integer', 'min' => 0],
                        ]);

                        if ($dynamicModel->hasErrors()) {
                            // validation fails
                            throw new BadRequestHttpException($dynamicModel->getFirstErrors());
                        } else {
                            // validation succeeds
                            /** Add new program for user  */
                            if ($userProgramAssignment === null) {
                                $model = UserProgram::create($program_uuid, $dynamicModel->session);
                                $model->link('user', $form->user);
                                if (!$model->save()) {
                                    Yii::error($model->errors);
                                }
                            }

                            if ($userProgramAssignment !== null) {
                                $userProgramAssignment->updateAttributes(['session_planed' => $dynamicModel->session]);
                            }
                        }
                    } else {
                        /** If program turn off. Delete relation user and program */
                        if ($userProgramAssignment !== null) {
                            $userProgramAssignment->delete();
                        }
                    }
                }
            }
        });
    }

    /**
     * Если у ментора или коуча по какой-то причине была подключена противоположная программа
     * мы её удаляем
     *
     * @param UserUpdateForm $form
     * @throws \Exception
     */
    public function manageMentorPrograms(UserUpdateForm $form)
    {
        $this->transaction->wrap(function () use ($form) {
            foreach ($form->user->programAssignments as $program) {
                switch ($form->user->role) {
                    case Rbac::ROLE_MENTOR:
                        if ($program->program_uuid === Program::COACH_UUID) {
                            $program->delete();
                        }
                        break;
                    case Rbac::ROLE_COACH:
                        if ($program->program_uuid === Program::MENTOR_UUID) {
                            $program->delete();
                        }
                        break;
                }
            }
        });
    }

    /**
     * todo вынести в отдельный кейс со своей зависимостью от Google
     * Проверяет токен на истечения срока жизни и если он истек обновляет
     * @param User $user
     */
    public function verifyAndUpdateGoogleToken(User $user): void
    {
        /**
         * @var Google $google
         */
        $google = Yii::$app->google;
        $accessToken = $user->getGoogleAccessToken();
        try {
            $accessToken->verify();
        } catch (DomainException $exception) {
            $user->setGoogleAccessToken($google->getAuth()->updateToken($accessToken));
        }
    }
}
