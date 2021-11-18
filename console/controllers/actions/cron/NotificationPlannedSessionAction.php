<?php

namespace console\controllers\actions\cron;

use common\access\Rbac;
use common\dispatchers\EventDispatcher;
use common\models\events\NotificationPlannedSession;
use common\models\TrainingSession;
use common\models\traits\EventTrait;
use common\models\UserTraining;
use common\services\TransactionManager;
use Yii;
use yii\base\Action;
use yii\console\ExitCode;

class NotificationPlannedSessionAction extends Action
{
    use EventTrait;

    private $dispatcher;

    public function __construct($id, $controller, EventDispatcher $dispatcher, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->dispatcher = $dispatcher;
    }


    /**
     * @return int
     * @throws \Exception
     */
    public function run()
    {
        $transaction = new TransactionManager();

        $transaction->wrap(function () {
            Yii::$app->db->createCommand('SET TIMEZONE = "UTC"')->execute();

            $userTrainings24 = $this->getTrainingsAsset(UserTraining::NOTIFY_STATUS_24_SEND);
            $userTrainings48 = $this->getTrainingsAsset(UserTraining::NOTIFY_STATUS_48_SEND);

            foreach ($userTrainings24 as $userTraining) {
                /** @var UserTraining $userTraining */
                $training = $userTraining->training;
                if (empty($training) || !$training->checkTimeToSendNotificationBeforeTrainingSession(24)) {
                    continue;
                }

                $userTraining->notify_status = UserTraining::NOTIFY_STATUS_24_SEND;
                $userTraining->save(false);
                $this->recordEvent(new NotificationPlannedSession($userTraining->user, $training));
            }
            foreach ($userTrainings48 as $userTraining) {
                /** @var UserTraining $userTraining */

                if (empty($training) || !$training->checkTimeToSendNotificationBeforeTrainingSession(48)) {
                    continue;
                }

                $userTraining->notify_status = UserTraining::NOTIFY_STATUS_48_SEND;
                $userTraining->save(false);
                $this->recordEvent(new NotificationPlannedSession($userTraining->user, $training));
            }

            $this->dispatcher->dispatchAll($this->events);
        });

        return ExitCode::OK;
    }

    private function getTrainingsAsset(int $notifyStatus)
    {
        $userTrainings = UserTraining::find()
            ->alias('ut')
            ->joinWith(
                ['user u', 'training ts'],
                true,
                'INNER JOIN'
            )
            ->andWhere([
                'u.role' => Rbac::ROLE_EMP,
            ])
            ->andWhere([
                'ts.status' => TrainingSession::STATUS_CONFIRM
            ]);

        if ($notifyStatus === UserTraining::NOTIFY_STATUS_24_SEND) {
            $userTrainings->andWhere([
                'IN',
                'ut.notify_status',
                [
                    UserTraining::NOTIFY_STATUS_NOT_SEND,
                    UserTraining::NOTIFY_STATUS_48_SEND
                ]
            ]);
        } else {
            $userTrainings->andWhere([
                'ut.notify_status' => UserTraining::NOTIFY_STATUS_NOT_SEND
            ]);
        }

        return $userTrainings->all();
    }
}
