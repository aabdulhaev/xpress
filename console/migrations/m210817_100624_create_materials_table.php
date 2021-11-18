<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%materials}}`.
 */
class m210817_100624_create_materials_table extends Migration
{
    public $tableName = '{{%materials}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if($this->db->driverName==='mysql'){
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            $this->tableName,
            [
                'material_uuid' => 'UUID NOT NULL UNIQUE',
                'type' => $this->smallInteger()->notNull()->defaultValue(1),
                'title' => $this->string(32)->notNull(),
                'description' => $this->string(2048),
                'body' => $this->text()->notNull(),
                'img_name' => $this->string(1048),

                'theme' => $this->string(255),
                'language_uuid' => 'UUID',
                'source_type' => $this->smallInteger(),
                'source_description' => $this->string(500),
                'learn_time' => $this->string(10),

                'status' => $this->smallInteger()->notNull()->defaultValue(1),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'approve_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'approve_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->addPrimaryKey('pk_materials', $this->tableName, 'material_uuid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
