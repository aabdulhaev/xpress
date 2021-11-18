<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%competence}}`.
 */
class m210330_003903_create_competence_table extends Migration
{
    public $tableName = '{{%competence}}';

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
                //'id' => $this->primaryKey(),
                'competence_uuid' => 'UUID NOT NULL UNIQUE',
                'title' => $this->string(32)->notNull(),
                'description' => $this->string(2048),
                'icon' => $this->string(1048),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );

        $this->addPrimaryKey('pk_competence', $this->tableName, 'competence_uuid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
