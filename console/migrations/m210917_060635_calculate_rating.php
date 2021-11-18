<?php

use common\models\traits\EventTrait;
use yii\db\Migration;

/**
 * Class m210917_060635_calculate_rating
 */
class m210917_060635_calculate_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sessionsQuery = \common\models\TrainingSession::find();
        foreach ($sessionsQuery->each() as $session) {
            /** @var \common\models\TrainingSession $session */
            $sessionRatingsQuery = $session->getSessionRatings();
            foreach ($sessionRatingsQuery->each() as $rate) {
                \common\components\helpers\RatingHelper::update($rate);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
