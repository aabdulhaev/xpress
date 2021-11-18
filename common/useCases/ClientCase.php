<?php

declare(strict_types=1);

namespace common\useCases;

use common\forms\ClientForm;
use common\models\Client;
use common\models\ClientCoach;
use common\models\ClientTariff;
use common\models\EmployeeMentor;
use common\models\TrainingSession;
use common\models\UserTraining;
use common\repositories\ClientRepository;
use common\repositories\EmployeeMentorRepository;
use common\repositories\ProgramRepository;
use common\repositories\TariffRepository;
use common\repositories\TrainingRepository;
use common\repositories\UserRepository;
use common\services\TransactionManager;
use Exception;

class ClientCase
{

    protected $userRepo;
    protected $clientRepo;
    protected $tariffRepo;
    protected $programRepo;
    protected $emRepo;
    protected $transaction;
    private $trainingRepo;

    public function __construct(
        UserRepository $userRepo,
        ClientRepository $clientRepo,
        TariffRepository $tariffRepo,
        ProgramRepository $programRepo,
        EmployeeMentorRepository $emRepo,
        TrainingRepository $trainingRepo,
        TransactionManager $transaction
    ) {
        $this->userRepo = $userRepo;
        $this->clientRepo = $clientRepo;
        $this->tariffRepo = $tariffRepo;
        $this->programRepo = $programRepo;
        $this->emRepo = $emRepo;
        $this->trainingRepo = $trainingRepo;
        $this->transaction = $transaction;
    }

    public function create(ClientForm $form): Client
    {
        $client = Client::create($form->name);

        $this->transaction->wrap(function () use ($client, $form) {
            $tariff = $this->tariffRepo->get($form->tariff_uuid);
            $expire_at = $tariff->getExpireTime() + time();
            $client->assignTariff($tariff->tariff_uuid, $expire_at);

            foreach ($form->programs as $program_uuid) {
                $program = $this->programRepo->get($program_uuid);
                $client->assignProgram($program->program_uuid);
            }

            if (count($form->coaches) > 0) {
                foreach ($form->coaches as $coach_uuid) {
                    $coach = $this->userRepo->get($coach_uuid);
                    $client->assignCoach($coach->user_uuid);
                }
            }

            $this->clientRepo->save($client);
        });

        return $client;
    }

    /**
     * @throws Exception
     */
    public function edit($client_uuid, ClientForm $form): Client
    {
        $client = $this->clientRepo->get($client_uuid);

        $this->transaction->wrap(function () use ($client, $form) {
            $client->name = $form->name;

            $tariff = $this->tariffRepo->get($form->tariff_uuid);
            $expired = $tariff->getExpireTime() + (60 * 60 * 24 * 30);
            $client->assignTariff($tariff->tariff_uuid, $expired);

            if (count($form->programs)) {
                foreach ($form->programs as $program_uuid) {
                    $client->assignProgram($program_uuid);
                }

                $programAssignments = $client->getProgramAssignments()->select(['program_uuid'])
                    ->column();

                $deleteAssignmentProgram = array_diff($programAssignments, $form->programs);
                if (!empty($deleteAssignmentProgram)) {
                    foreach ($deleteAssignmentProgram as $program_uuid) {
                        $client->revokeProgram($program_uuid);
                    }
                }
            } else {
                $client->revokePrograms();
            }

            $coachAssignments = $client->coachAssignments;
            $coachAssignmentIds = array_column($coachAssignments, 'coach_uuid');

            if (count($form->coaches) > 0) {
                $revokedCoaches = array_diff($coachAssignmentIds, $form->coaches);

                foreach ($coachAssignments as $coachAssignment) {
                    if (in_array($coachAssignment->coach_uuid, $revokedCoaches)) {
                        $this->updateClientCoach($coachAssignment, ClientCoach::STATUS_DECLINE);
                    } else {
                        $this->updateClientCoach($coachAssignment, ClientCoach::STATUS_APPROVED);
                    }
                }

                $client->assignCoaches($form->coaches);
            } else {
                foreach ($coachAssignments as $coachAssignment) {
                    $this->updateClientCoach($coachAssignment, ClientCoach::STATUS_DECLINE);
                }
                $client->revokeCoaches();
            }

            $this->clientRepo->save($client);
        });

        return $client;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function unsetCoaches(array $coaches_uuid, string $client_uuid): bool
    {
        $client = $this->clientRepo->get($client_uuid);
        $coachAssignments = $client->coachAssignments;
        foreach ($coachAssignments as $coachAssignment) {
            if (!in_array($coachAssignment->coach_uuid, $coaches_uuid)) {
                $coachAssignment->delete();
            }
        }
        $client->coachAssignments = $coachAssignments;

        return $client->save();
    }

    public function updateClientCoach(ClientCoach $coachAssignment, int $status): void
    {
        if ($coachAssignment->status === $status) {
            return;
        }

        $disconnect = $status !== ClientCoach::STATUS_APPROVED;

        $employeeMentors = $this->emRepo->getByMentorAndClientUuid(
            $coachAssignment->coach_uuid,
            $coachAssignment->client_uuid,
            $disconnect ? EmployeeMentor::STATUS_APPROVED : EmployeeMentor::STATUS_UNCONNECTED
        );
        $employees = array_column($employeeMentors, 'employee_uuid');

        if (count($employees)) {
            EmployeeMentor::updateAll(
                [
                    'status' => $disconnect ? EmployeeMentor::STATUS_UNCONNECTED : EmployeeMentor::STATUS_APPROVED
                ],
                [
                    'AND',
                    ['mentor_uuid' => $coachAssignment->coach_uuid],
                    ['IN', 'employee_uuid', $employees]
                ]
            );
        }

        $trainingsSession = $this->trainingRepo->getByMentorAndEmployees(
            $coachAssignment->coach_uuid,
            $employees
        );
        $trainings = array_column($trainingsSession, 'training_uuid');
        if ($trainings) {
            TrainingSession::updateAll(
                ['status' => $disconnect ? TrainingSession::STATUS_DELETED : TrainingSession::STATUS_NOT_CONFIRM],
                ['IN', 'training_uuid', $trainings]
            );
            UserTraining::updateAll(
                ['status' => $disconnect ? UserTraining::STATUS_DELETED : UserTraining::STATUS_NOT_CONFIRM],
                ['IN', 'training_uuid', $trainings]
            );
        }
    }
}
