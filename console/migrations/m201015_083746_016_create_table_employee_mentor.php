<?php

use yii\db\Migration;

class m201015_083746_016_create_table_employee_mentor extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%employee_mentor}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'employee_uuid' => 'UUID NOT NULL',
                'mentor_uuid' => 'UUID NOT NULL',
                'status' => $this->tinyInteger(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',

                'comment' => $this->text()
            ],
            $tableOptions
        );

        $this->createIndex('employee_mentor_id_key', $tableName, ['employee_uuid','mentor_uuid'], true);
        $this->addPrimaryKey('pk_employee_mentor', $tableName, ['employee_uuid','mentor_uuid']);
    }

    public function down()
    {
        $this->dropTable('{{%employee_mentor}}');
    }
}
