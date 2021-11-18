<?php

use yii\db\Migration;

/**
 * Class m210831_120047_add_primary_key_in_user_meeting_table
 */
class m210831_120047_add_primary_key_in_user_meeting_table extends Migration
{
    public $tableName = '{{%user_meeting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'user_meeting_uuid','UUID NOT NULL UNIQUE');

        $this->addPrimaryKey('userMeeting', $this->tableName, 'user_meeting_uuid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210831_120047_add_primary_key_in_user_meeting_table cannot be reverted.\n";

        return false;
    }
}
