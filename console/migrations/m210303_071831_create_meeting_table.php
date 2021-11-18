<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%meeting}}`.
 */
class m210303_071831_create_meeting_table extends Migration
{

    public $tableName = '{{%meeting}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'meeting_uuid' => 'UUID NOT NULL',
            'training_uuid' => 'UUID NOT NULL',
            'status' => $this->smallInteger()->notNull(),
            'start_at' => 'TIMESTAMP WITH TIME ZONE NOT NULL',
            'end_at' => 'TIMESTAMP WITH TIME ZONE',


            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->defaultValue(null),
            'created_by' => 'UUID NOT NULL',
            'updated_by' => 'UUID',
        ]);

        $this->addPrimaryKey('pk_meeting', $this->tableName, 'meeting_uuid');
        $this->addForeignKey(
            'fk_meeting_training',
            $this->tableName,
            'training_uuid',
            '{{%training_session}}',
            'training_uuid',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_meeting_creator',
            $this->tableName,
            'created_by',
            '{{%user}}',
            'user_uuid',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_meeting_editor',
            $this->tableName,
            'updated_by',
            '{{%user}}',
            'user_uuid',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex(
            'idx_meeting_training',
            $this->tableName,
            'training_uuid',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%meeting}}');
    }
}
