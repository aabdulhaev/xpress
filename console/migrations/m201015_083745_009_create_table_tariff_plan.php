<?php

use yii\db\Migration;

class m201015_083745_009_create_table_tariff_plan extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableName = '{{%tariff_plan}}';

        $this->createTable(
            $tableName,
            [
                //'id' => $this->primaryKey(),
                'tariff_uuid' => 'UUID NOT NULL UNIQUE',
                'name' => $this->string(32)->notNull(),
                'description' => $this->string(20148)->notNull(),
                'cost' => $this->double()->notNull(),
                'constraints' => $this->json()->notNull(),

                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->defaultValue(null),
                'blocked_at' => $this->integer()->defaultValue(null),

                'created_by' => 'UUID NOT NULL',
                'updated_by' => 'UUID',
                'blocked_by' => 'UUID',
            ],
            $tableOptions
        );
        $this->addPrimaryKey('pk_tariff_plan', $tableName, 'tariff_uuid');
    }

    public function down()
    {
        $this->dropTable('{{%tariff_plan}}');
    }
}
