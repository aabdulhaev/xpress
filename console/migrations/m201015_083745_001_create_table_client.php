<?php

use yii\db\Migration;

class m201015_083745_001_create_table_client extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%client}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'client_uuid' => 'UUID NOT NULL UNIQUE',
                'name' => $this->string()->notNull(),
                'status' => $this->smallInteger()->notNull(),
                'logo' => $this->string(1024)->null(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->addPrimaryKey('pk_client', $tableName, 'client_uuid');

    }

    public function down()
    {
        $this->dropTable('{{%client}}');
    }
}
