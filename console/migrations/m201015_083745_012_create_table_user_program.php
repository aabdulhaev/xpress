<?php

use yii\db\Migration;

class m201015_083745_012_create_table_user_program extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%user_program}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'user_uuid' => 'UUID NOT NULL',
                'program_uuid' => 'UUID NOT NULL',

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->createIndex('user_program_program_id_key', $tableName, ['user_uuid','program_uuid'], true);
        $this->addPrimaryKey('pk_user_program', $tableName, ['user_uuid','program_uuid']);
    }

    public function down()
    {
        $this->dropTable('{{%user_program}}');
    }
}
