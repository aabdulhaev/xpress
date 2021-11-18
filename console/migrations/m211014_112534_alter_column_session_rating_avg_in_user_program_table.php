<?php

use yii\db\Migration;

/**
 * Class m211014_112534_alter_column_session_rating_avg_in_user_program_table
 */
class m211014_112534_alter_column_session_rating_avg_in_user_program_table extends Migration
{
    public $tableName = '{{%user_program}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'session_rating_avg', $this->float()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'session_rating_avg', $this->integer()->defaultValue(0));
    }
}
