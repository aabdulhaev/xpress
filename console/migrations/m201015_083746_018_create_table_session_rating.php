<?php

use yii\db\Migration;

class m201015_083746_018_create_table_session_rating extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%session_rating}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'user_uuid' => 'UUID NOT NULL',
                'training_uuid' => 'UUID NOT NULL',
                'comment' => $this->text()->notNull(),
                'rate' => $this->smallInteger()->notNull(),
                'addon' => $this->json()->null(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',

                'is_calculated' => $this->boolean()->defaultValue(false)
            ],
            $tableOptions
        );

        $this->createIndex('user_session_rating_id_key', $tableName, ['user_uuid','training_uuid'], true);
        $this->addPrimaryKey('pk_session_rating', $tableName, ['user_uuid','training_uuid']);
    }

    public function down()
    {
        $this->dropTable('{{%session_rating}}');
    }
}
