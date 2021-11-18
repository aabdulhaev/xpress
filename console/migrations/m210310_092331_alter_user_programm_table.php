<?php

use yii\db\Migration;

/**
 * Class m210310_092331_alter_user_programm_table
 */
class m210310_092331_alter_user_programm_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%user_program}}';
        $this->addColumn($tableName, 'session_planed', $this->integer()->defaultValue(0));
        $this->addColumn($tableName, 'session_complete', $this->integer()->defaultValue(0));
        $this->addColumn($tableName, 'session_rating_avg', $this->integer()->defaultValue(0));
        $this->addColumn($tableName, 'session_cancel', $this->integer()->defaultValue(0));

        $this->addForeignKey('fk_user_program_user', $tableName,'user_uuid','{{%user}}', 'user_uuid', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_user_program_program', $tableName,'program_uuid','{{%program}}', 'program_uuid', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210310_092331_alter_user_programm_table cannot be reverted.\n";

        return false;
    }
}
