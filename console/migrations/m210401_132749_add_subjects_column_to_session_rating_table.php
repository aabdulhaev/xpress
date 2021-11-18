<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%session_rating}}`.
 */
class m210401_132749_add_subjects_column_to_session_rating_table extends Migration
{
    public $tableName = '{{%session_rating}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'subjects', 'UUID[] NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'subjects');
    }
}
