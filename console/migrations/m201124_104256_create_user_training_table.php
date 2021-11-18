<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_training}}`.
 */
class m201124_104256_create_user_training_table extends Migration
{

    public $tableName = '{{%user_training}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'user_uuid' => 'UUID NOT NULL',
            'training_uuid' => 'UUID NOT NULL',
            'status' => $this->tinyInteger(),
            'comment' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->defaultValue(null),
            'blocked_at' => $this->integer()->defaultValue(null),

            'created_by' => 'UUID NOT NULL',
            'updated_by' => 'UUID',
            'blocked_by' => 'UUID',
        ]);

        $this->createIndex('user_training_id_key', $this->tableName, ['user_uuid','training_uuid'], true);
        $this->addPrimaryKey('pk_user_training', $this->tableName, ['user_uuid','training_uuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_training}}');
    }
}
