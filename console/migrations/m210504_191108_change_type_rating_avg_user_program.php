<?php

use yii\db\Migration;

/**
 * Class m210504_191108_change_type_rating_avg_user_program
 */
class m210504_191108_change_type_rating_avg_user_program extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%session_rating}}','rate',$this->smallInteger());
        $this->alterColumn('{{%user_program}}','session_rating_avg',$this->decimal(10,1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%session_rating}}','rate',$this->decimal(10,1));
        $this->alterColumn('{{%user_program}}','session_rating_avg',$this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210504_191108_change_type_rating_avg_user_program cannot be reverted.\n";

        return false;
    }
    */
}
