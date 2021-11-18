<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_training}}`.
 */
class m210904_140009_add_notify_status_column_to_user_training_table extends Migration
{
    public $tableName = '{{%user_training}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'notify_status',
            $this->tinyInteger(2)
                ->notNull()
                ->defaultValue(0)
                ->comment('User notify status')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'notify_status');
    }
}
