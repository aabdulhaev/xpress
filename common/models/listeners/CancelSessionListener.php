<?php

namespace common\models\listeners;

use common\models\events\CancelSession;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserProgram;

class CancelSessionListener
{

    public function handle(CancelSession $event): void
    {
        /** @var TrainingSession $session */
        $session = $event->session;

        /** @var User $employee */
        $employee = $session->employee;
        /** @var User $mentor */
        $mentor = $session->coachOrMentor;
        $program_uuid = $session->program_uuid;

        /** @var UserProgram $empProgram */
        $empProgram = $employee->getProgramAssignments()
            ->andWhere(['program_uuid' => $program_uuid])
            ->one();

        try {
            if ($empProgram) {
                $empCanceled = $employee->getTrainings()
                    ->andWhere(['status' => TrainingSession::STATUS_CANCEL])
                    ->count();
                $empProgram->session_cancel = $empCanceled;
                $empProgram->save();
            }

            /** @var UserProgram $mentProgram */
            $mentProgram = $mentor->getProgramAssignments()
                ->andWhere(['program_uuid' => $program_uuid])
                ->one();
            if ($mentProgram) {
                $mentCanceled = $mentor->getTrainings()
                    ->andWhere(['status' => TrainingSession::STATUS_CANCEL])
                    ->count();
                $mentProgram->session_cancel = $mentCanceled;
                $mentProgram->save();
            }
        } catch (\PDOException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
