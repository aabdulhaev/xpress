<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_meeting}}`.
 */
class m210817_083831_create_user_meeting_table extends Migration
{
    public $tableName = '{{%user_meeting}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            $this->tableName,
            [
                'meeting_uuid' => 'UUID NOT NULL',
                'user_uuid' => 'UUID',
                'email' => $this->string(),
                'status' => $this->tinyInteger()->defaultValue(0),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->createIndex(
            'pk_user_meeting',
            $this->tableName,
            ['meeting_uuid','user_uuid'],
            true
        );

        $this->addForeignKey(
            'fk_user_meeting_user',
            $this->tableName,
            'user_uuid',
            '{{%user}}',
            'user_uuid',
            'SET NULL',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
