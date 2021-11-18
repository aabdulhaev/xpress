<?php

use yii\db\Migration;

class m201015_083745_013_create_table_user_subject extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%user_subject}}';

        $this->createTable(
            $tableName,
            [
                'user_uuid' => 'UUID NOT NULL',
                'subject_uuid' => 'UUID NOT NULL',
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
            ],
            $tableOptions
        );

        $this->createIndex('user_subject_subject_id_key', $tableName, ['user_uuid', 'subject_uuid'], true);
        $this->addPrimaryKey('pk_user_subject', $tableName, ['user_uuid', 'subject_uuid']);
    }

    public function down()
    {
        $this->dropTable('{{%user_subject}}');
    }
}
