<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%training_session}}`.
 */
class m210913_115420_add_moved_by_role_column_to_training_session_table extends Migration
{
    public $tableName = '{{%training_session}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'moved_by_role', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'moved_by_role');
    }
}
