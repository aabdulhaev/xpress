<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_sections}}`.
 */
class m210901_091959_create_user_sections_table extends Migration
{
    public $tableName = '{{%user_sections}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            $this->tableName,
            [
                'user_uuid' => 'UUID NOT NULL',
                'section_uuid' => 'UUID NOT NULL',

                'status' => $this->smallInteger()->notNull()->defaultValue(1),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->createIndex('uk_user_sections', $this->tableName, ['user_uuid', 'section_uuid'], true);

        $this->addPrimaryKey('pk_user_sections', $this->tableName, ['user_uuid', 'section_uuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
