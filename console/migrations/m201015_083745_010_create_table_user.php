<?php

use yii\db\Migration;

class m201015_083745_010_create_table_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%user}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'user_uuid' => 'UUID NOT NULL UNIQUE',
                'email' => $this->string()->notNull()->unique(),
                'first_name' => $this->string(32)->notNull(),
                'last_name' => $this->string(32)->notNull(),
                'password_hash' => $this->string()->notNull(),
                'password_reset_token' => $this->string()->unique(),
                'phone' => $this->string(12),
                'status' => $this->smallInteger()->notNull()->defaultValue(0),
                'avatar' => $this->string(1048),
                'client_uuid' => 'UUID',
                'department' => $this->string(64)->defaultValue(null),
                'position' => $this->string(64)->defaultValue(null),
                'certification' => $this->string(1024),
                'level' => $this->tinyInteger(),
                'role' => $this->string(32)->notNull(),
                'auth_key' => $this->string(32)->notNull(),
                'verification_token' => $this->string()->defaultValue(null),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),
                'created_by' => 'UUID',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );
        $this->addPrimaryKey('pk_user', $tableName, 'user_uuid');

        $this->createTable('{{%user_stat}}', [
            'user_uuid' => 'UUID NOT NULL UNIQUE',
            'mentor_rating' => $this->integer(),
            'coach_rating' => $this->integer(),
            'mentor_session_completed' => $this->integer(),
            'mentor_session_planned' => $this->integer(),
            'mentor_session_canceled' => $this->integer(),
            'coach_session_completed' => $this->integer(),
            'coach_session_planned' => $this->integer(),
            'coach_session_canceled' => $this->integer()
        ]);

        $this->addPrimaryKey('pk_user_stat', '{{%user_stat}}', 'user_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
