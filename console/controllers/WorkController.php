<?php

declare(strict_types=1);

namespace console\controllers;

use common\access\Rbac;
use common\components\helpers\RatingHelper;
use common\models\Program;
use common\models\SessionRating;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserProgram;
use common\repositories\UserProgramRepository;
use Ramsey\Uuid\Uuid;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Console;

class WorkController extends Controller
{
    public $userProgramRepo;

    public function __construct(
        $id,
        $module,
        UserProgramRepository $userProgramRepo,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userProgramRepo = $userProgramRepo;
    }


    public function actionIndex()
    {
    }

    /**
     * Получить нужное количество uuid
     *
     * @param int $count количество идентификаторов (не больше 100)
     */
    public function actionUuid(int $count): int
    {
        if ($count > 100) {
            $this->stderr('Вы указали слишком большое количество' . PHP_EOL);
            return ExitCode::DATAERR;
        }

        while ($count) {
            $this->stdout(Uuid::uuid6() . PHP_EOL);
            --$count;
        }

        return ExitCode::OK;
    }

    /**
     * Изменить временную зону сессий
     */
    public function actionChangeTimezone(): int
    {
        $query = new Query();
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $query->createCommand()
                ->setRawSql(
                    "UPDATE training_session SET start_at_tc = start_at_tc - '3 hour'::interval
                WHERE to_timestamp(created_at) BETWEEN now() - '2 month'::interval AND now()"
                )->execute();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->stderr($e->getMessage() . PHP_EOL);
            $this->stderr($e->getTraceAsString() . PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('Table training_session update successful' . PHP_EOL);
        return ExitCode::OK;
    }

    public function actionFixtureUp()
    {
        echo shell_exec('php yii fixture "*, -yii\test\InitDbFixture" --interactive=0');
    }

    public function actionCalculateSessionRatings()
    {
        $sessionsQuery = TrainingSession::find()
            ->andWhere(['in', TrainingSession::tableName(). '.status', [TrainingSession::STATUS_COMPLETED, TrainingSession::STATUS_RATED]]);
        foreach ($sessionsQuery->each() as $session) {
            /** @var TrainingSession $session */

            $this->stdout("Calculated ratings for session #{$session->training_uuid}.\n", Console::FG_GREEN);

            /** @var \common\models\TrainingSession $session */
            $sessionRatingsQuery = $session->getSessionRatings();
            foreach ($sessionRatingsQuery->each() as $rate) {
                /** @var SessionRating $rate */
                RatingHelper::update($rate);
            }
        }
    }

    public function actionCalculateTrainingsNumber()
    {
        /** @var ActiveQuery $trainingSessionsQuery */
        $trainingSessionsQuery = TrainingSession::find()->where([
            'in',
            TrainingSession::tableName() . '.status',
            [TrainingSession::STATUS_COMPLETED, TrainingSession::STATUS_RATED]
        ]);

        foreach ($trainingSessionsQuery->each() as $trainingSession) {
            /** @var TrainingSession $trainingSession */

            $this->stdout("Set trainings number for session #{$trainingSession->training_uuid}.\n", Console::FG_GREEN);
            $trainingSession->setSessionComplete();
        }
    }
}
