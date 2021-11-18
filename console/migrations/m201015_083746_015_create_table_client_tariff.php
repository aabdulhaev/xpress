<?php

use yii\db\Migration;

class m201015_083746_015_create_table_client_tariff extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%client_tariff}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'client_tariff_uuid' => 'UUID NOT NULL',
                'client_uuid' => 'UUID NOT NULL',
                'tariff_uuid' => 'UUID NOT NULL',
                'expire_at' => $this->integer()->notNull(),
                'status' => $this->smallInteger()->notNull(),
                'constraint_used' => $this->json()->notNull(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );
        $this->addPrimaryKey('pk_client_tariff', $tableName, 'client_tariff_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%client_tariff}}');
    }
}
