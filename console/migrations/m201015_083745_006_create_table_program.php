<?php

use yii\db\Migration;

class m201015_083745_006_create_table_program extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%program}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'program_uuid' => 'UUID NOT NULL UNIQUE',
                'name' => $this->string(32)->notNull(),
                'description' => $this->string(2048)->notNull(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );
        $this->addPrimaryKey('pk_program', $tableName, 'program_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%program}}');
    }
}
