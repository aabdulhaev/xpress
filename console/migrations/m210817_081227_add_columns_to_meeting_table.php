<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%meeting}}`.
 */
class m210817_081227_add_columns_to_meeting_table extends Migration
{
    public $tableName = '{{%meeting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'title', $this->string());
        $this->addColumn($this->tableName, 'description', $this->text());
        $this->addColumn($this->tableName, 'group_link', $this->string());
        $this->addColumn($this->tableName, 'type', $this->tinyInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'title');
        $this->dropColumn($this->tableName, 'description');
        $this->dropColumn($this->tableName, 'group_link');
        $this->dropColumn($this->tableName, 'type');
    }
}
