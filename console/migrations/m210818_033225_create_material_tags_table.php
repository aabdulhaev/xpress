<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%material_tags}}`.
 */
class m210818_033225_create_material_tags_table extends Migration
{
    public $tableName = '{{%material_tags}}';

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
                'material_uuid' => 'UUID NOT NULL',
                'tag_uuid' => 'UUID NOT NULL',

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

        $this->createIndex('uk_material_tags', $this->tableName, ['material_uuid', 'tag_uuid'], true);
        $this->addPrimaryKey('pk_material_tags', $this->tableName, ['material_uuid', 'tag_uuid']);

        $this->addForeignKey(
            'fk_material_tags_material',
            $this->tableName,
            'material_uuid',
            '{{%materials}}',
            'material_uuid',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_material_tags_tag',
            $this->tableName,
            'tag_uuid',
            '{{%tags}}',
            'tag_uuid',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
