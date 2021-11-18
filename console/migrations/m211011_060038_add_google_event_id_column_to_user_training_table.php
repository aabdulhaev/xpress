<?php

use common\models\UserTraining;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_training}}`.
 */
class m211011_060038_add_google_event_id_column_to_user_training_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserTraining::tableName(), 'google_event_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserTraining::tableName(), 'google_event_id');
    }
}
