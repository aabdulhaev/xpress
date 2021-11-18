<?php

use yii\db\Migration;

/**
 * Class m211012_143519_add_column_notify_status_to_user_meeting_table
 */
class m211012_143519_add_column_notify_status_to_user_meeting_table extends Migration
{
    public $tableName = '{{%user_meeting}}';

    /**
     * @return bool|void
     * @throws ReflectionException
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'notify_status',
            $this->tinyInteger(2)
                ->notNull()
                ->defaultValue(0)
                ->comment('user meeting notify status')
        );
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'notify_status');
    }
}
