<?php

use yii\db\Migration;

/**
 * Class m211008_070848_add_colum_token_to_user_meeting_table
 */
class m211008_070848_add_colum_token_to_user_meeting_table extends Migration
{
    public $tableName = '{{%user_meeting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'token', $this->string(32)->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'token');
    }
}
