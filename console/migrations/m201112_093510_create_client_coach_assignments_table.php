<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_coach}}`.
 */
class m201112_093510_create_client_coach_assignments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = '{{%client_coach}}';

        $this->createTable($tableName, [
            'client_uuid' => 'UUID NOT NULL',
            'coach_uuid' => 'UUID NOT NULL',
            'status' => $this->tinyInteger(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->defaultValue(null),
            'blocked_at' => $this->integer()->defaultValue(null),

            'created_by' => 'UUID NOT NULL',
            'updated_by' => 'UUID',
            'blocked_by' => 'UUID',
        ]);

        $this->createIndex('client_coach_id_key', $tableName, ['client_uuid','coach_uuid'], true);
        $this->addPrimaryKey('pk_client_coach', $tableName, ['client_uuid','coach_uuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_coach}}');
    }
}
