<?php

namespace console\controllers\actions\cron;

use common\models\TrainingSession;
use common\repositories\TrainingRepository;
use Exception;
use Yii;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\ActiveQuery;

class CancelExpiredSessionAction extends Action
{
    public $sessionRepository;

    public function __construct(
        $id,
        $controller,
        TrainingRepository $sessionRepository,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->sessionRepository = $sessionRepository;
    }


    /**
     * Смена статуса сессии на "Отменена", если в течение 30 минут с начала сессии коуч/ментор не начал сессию
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @return int
     */
    public function run()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            /** @var ActiveQuery $sessionsQuery */
            $sessionsQuery = TrainingSession::find()
                ->andWhere(['in', 'status', [TrainingSession::STATUS_NOT_CONFIRM, TrainingSession::STATUS_CONFIRM]]);
            foreach ($sessionsQuery->each() as $session) {
                /** @var TrainingSession $session */
                if ($session->isExpired()) {
                    $session->toCancel();
                    $this->sessionRepository->save($session);
                }
            }

            $transaction->commit();
        } catch (Exception $e) {
            Yii::error($e->getMessage() . "\n" . $e->getFile() . "(" . $e->getLine() . ")\n" . $e->getTraceAsString());
            $transaction->rollBack();
            throw $e;
        }

        return ExitCode::OK;
    }
}
