<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%material_subjects}}`.
 */
class m210818_033108_create_material_subjects_table extends Migration
{
    public $tableName = '{{%material_subjects}}';

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
                'subject_uuid' => 'UUID NOT NULL',

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

        $this->createIndex('uk_material_subjects', $this->tableName, ['material_uuid', 'subject_uuid'], true);
        $this->addPrimaryKey('pk_material_subject', $this->tableName, ['material_uuid', 'subject_uuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
