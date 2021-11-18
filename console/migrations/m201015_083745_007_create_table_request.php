<?php

use yii\db\Migration;

class m201015_083745_007_create_table_request extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%request}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'request_uuid' => 'UUID NOT NULL UNIQUE',
                'name' => $this->string(64)->notNull(),
                'email' => $this->string(64)->notNull(),
                'phone' => $this->string(12)->notNull(),
                'type' => $this->smallInteger()->notNull(),
                'description' => $this->string(2048)->notNull(),
                'status' => $this->smallInteger()->notNull(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );
        $this->addPrimaryKey('pk_request', $tableName, 'request_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%request}}');
    }
}
