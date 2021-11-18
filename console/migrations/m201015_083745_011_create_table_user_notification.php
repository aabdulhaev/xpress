<?php

use yii\db\Migration;

class m201015_083745_011_create_table_user_notification extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%user_notification}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'user_uuid' => 'UUID NOT NULL',
                'notification_uuid' => 'UUID NOT NULL',
                'status' => $this->smallInteger()->notNull(),
                'channel' => $this->smallInteger()->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex('user_notification_notification_uuid_key', $tableName, ['user_uuid','notification_uuid'], true);
        $this->addPrimaryKey('pk_user_notification', $tableName, ['user_uuid', 'notification_uuid']);
    }

    public function down()
    {
        $this->dropTable('{{%user_notification}}');
    }
}
