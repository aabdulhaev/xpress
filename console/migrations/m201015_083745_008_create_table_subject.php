<?php

use yii\db\Migration;

class m201015_083745_008_create_table_subject extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%subject}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'subject_uuid' => 'UUID NOT NULL UNIQUE',
                'title' => $this->string(32)->notNull(),
                'description' => $this->string(2048),
                'icon' => $this->string(1048),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->addPrimaryKey('pk_subject', $tableName, 'subject_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%subject}}');
    }
}
