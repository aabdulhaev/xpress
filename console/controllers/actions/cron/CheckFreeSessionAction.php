<?php

namespace console\controllers\actions\cron;

use common\models\TrainingSession;
use common\models\UserTraining;
use Exception;
use Yii;
use yii\base\Action;
use yii\console\ExitCode;

class CheckFreeSessionAction extends Action
{
    /**
     * Checking free sessions to expired time and change this status to deleted
     * @throws \yii\db\Exception
     * @return int
     */
    public function run()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            Yii::$app->db->createCommand('SET TIMEZONE = "UTC"')->execute();
            Yii::$app->db->createCommand('
                UPDATE user_training ut1
                SET status = ' . UserTraining::STATUS_DELETED . '
                WHERE ut1.training_uuid IN (
                    SELECT ts.training_uuid
                    FROM training_session ts
                    WHERE ts.status != ' . TrainingSession::STATUS_DELETED . '
                      AND ts.start_at_tc < current_timestamp
                      AND ts.training_uuid IN (
                        SELECT ut.training_uuid
                        FROM user_training ut
                        WHERE ut.status != ' . UserTraining::STATUS_DELETED . '
                        GROUP BY ut.training_uuid
                        HAVING COUNT(ut.user_uuid) < 2
                    )
                    GROUP BY ts.training_uuid
                    ORDER BY ts.training_uuid
                );
            ')->execute();
            Yii::$app->db->createCommand('
                UPDATE training_session ts
                SET status = ' . TrainingSession::STATUS_DELETED . '
                WHERE ts.training_uuid IN (
                    SELECT ts1.training_uuid
                    FROM training_session ts1
                    WHERE ts1.status != ' . TrainingSession::STATUS_DELETED . '
                      AND ts1.start_at_tc < current_timestamp
                      AND ts1.training_uuid IN (
                        SELECT ut.training_uuid
                        FROM user_training ut
                        WHERE ut.status = ' . UserTraining::STATUS_DELETED . '
                        GROUP BY ut.training_uuid
                        HAVING COUNT(ut.user_uuid) < 2
                    )
                    ORDER BY created_at DESC
                );
            ')->execute();

            $transaction->commit();
        } catch (Exception $e) {
            Yii::error($e->getMessage() . "\n" . $e->getFile() . "(" . $e->getLine() . ")\n" . $e->getTraceAsString());
            $transaction->rollBack();
            throw $e;
        }

        return ExitCode::OK;
    }
}
