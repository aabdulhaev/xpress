<?php

use yii\db\Migration;

class m201015_083745_005_create_table_notification extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%notification}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'notification_uuid' => 'UUID NOT NULL UNIQUE',
                'type' => $this->integer()->notNull(),
                'body' => $this->text()->notNull(),
                'title' => $this->string()->notNull(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->addPrimaryKey('pk_notification', $tableName, 'notification_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%notification}}');
    }
}
