<?php

use yii\db\Migration;

/**
 * Class m210928_093137_change_column_training_uuid_in_meeting_table
 */
class m210928_093137_change_column_training_uuid_in_meeting_table extends Migration
{
    public $tableName = '{{%meeting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'training_uuid', 'UUID');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'training_uuid', 'UUID NOT NULL');
    }
}
