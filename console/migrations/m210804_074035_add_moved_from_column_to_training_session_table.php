<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%training_session}}`.
 */
class m210804_074035_add_moved_from_column_to_training_session_table extends Migration
{
    public $tableName = '{{%training_session}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'moved_from', 'UUID NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'moved_from');
    }
}
