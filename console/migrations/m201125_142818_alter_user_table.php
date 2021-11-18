<?php

use yii\db\Migration;

/**
 * Class m201125_142818_alter_user_table
 */
class m201125_142818_alter_user_table extends Migration
{

    public $tableName = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName,'time_zone', $this->string()->defaultValue('Europe/Moscow'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_142818_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_142818_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
