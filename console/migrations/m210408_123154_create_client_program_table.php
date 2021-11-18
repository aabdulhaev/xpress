<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_program}}`.
 */
class m210408_123154_create_client_program_table extends Migration
{
    public $tableName = '{{%client_program}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'client_uuid' => 'UUID NOT NULL',
            'program_uuid' => 'UUID NOT NULL',
            'status' => $this->smallInteger()->notNull(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->defaultValue(null),
            'blocked_at' => $this->integer()->defaultValue(null),

            'created_by' => 'UUID NOT NULL',
            'updated_by' => 'UUID',
            'blocked_by' => 'UUID',
        ]);

        $this->createIndex(
            'client_program_id_key',
            $this->tableName,
            ['client_uuid','program_uuid'],
            true
        );
        $this->addPrimaryKey(
            'pk_client_program',
            $this->tableName,
            ['client_uuid','program_uuid']
        );

        $this->addForeignKey(
          'fk_client_program_client',
            $this->tableName,
            'client_uuid',
            '{{%client}}',
            'client_uuid'
        );
        $this->addForeignKey(
            'fk_client_program_program',
            $this->tableName,
            'program_uuid',
            '{{%program}}',
            'program_uuid'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_client_program_program', $this->tableName);
        $this->dropForeignKey('fk_client_program_client', $this->tableName);
        $this->dropIndex('client_program_id_key', $this->tableName);
        $this->dropPrimaryKey('pk_client_program', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
