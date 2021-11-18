<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_competence}}`.
 */
class m210330_004344_create_user_competence_table extends Migration
{
    public $tableName = '{{%user_competence}}';

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
                'competence_uuid' => 'UUID NOT NULL',
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
            ],
            $tableOptions
        );

        $this->createIndex(
            'user_competence_competence_id_key',
            $this->tableName,
            ['user_uuid', 'competence_uuid'],
            true
        );
        $this->addPrimaryKey(
            'pk_user_competence',
            $this->tableName,
            ['user_uuid', 'competence_uuid']
        );

        $this->addForeignKey(
            'fk_user_competence_user',
            $this->tableName,
            'user_uuid',
            '{{%user}}',
            'user_uuid',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_user_competence_competence',
            $this->tableName,
            'competence_uuid',
            '{{%competence}}',
            'competence_uuid',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_competence_user',$this->tableName);
        $this->dropForeignKey('fk_user_competence_competence',$this->tableName);

        $this->dropTable($this->tableName);
    }
}
