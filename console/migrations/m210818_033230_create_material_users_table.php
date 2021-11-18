<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%material_user_actions}}`.
 */
class m210818_033230_create_material_users_table extends Migration
{
    public $tableName = '{{%material_users}}';

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
                'user_uuid' => 'UUID NOT NULL',

                'accessed' => $this->smallInteger()->notNull()->defaultValue(0),
                'elected' => $this->smallInteger()->notNull()->defaultValue(0),
                'learned' => $this->smallInteger()->notNull()->defaultValue(0),

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

        $this->addPrimaryKey('pk_material_user_action', $this->tableName, ['user_uuid', 'material_uuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
