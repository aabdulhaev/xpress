<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sections}}`.
 */
class m210901_091953_create_sections_table extends Migration
{
    public $tableName = '{{%sections}}';

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
                'section_uuid' => 'UUID NOT NULL UNIQUE',
                'title' => $this->string(32)->notNull(),
                'description' => $this->string(2048),

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

        $this->createIndex('uk_section_title', $this->tableName, ['title'], true);
        $this->addPrimaryKey('pk_section', $this->tableName, 'section_uuid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
