<?php

use yii\db\Migration;

/**
 * Class m210831_112439_alter_column_group_link_in_meeting_table
 */
class m210831_112439_alter_column_group_link_in_meeting_table extends Migration
{
    public $tableName = '{{%meeting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'group_link', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'group_link', $this->string());
    }
}
