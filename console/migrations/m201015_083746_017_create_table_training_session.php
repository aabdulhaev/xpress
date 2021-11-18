<?php

use yii\db\Migration;

class m201015_083746_017_create_table_training_session extends Migration
{

    public $tableName = '{{%training_session}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            $this->tableName,
            [
                //'id' => $this->primaryKey(),
                'training_uuid' => 'UUID NOT NULL',
                'status' => $this->smallInteger()->notNull(),
                'subject_uuid' => 'UUID NOT NULL',
                'start_at_tc' => 'TIMESTAMP WITH TIME ZONE NOT NULL',
                'duration' => $this->integer(),
                'service_link' => $this->string(1048),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );
        $this->addPrimaryKey('pk_training_session', $this->tableName, 'training_uuid');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
