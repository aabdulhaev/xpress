<?php

use yii\db\Migration;

/**
 * Class m210504_175218_change_rate_type_session_rating
 */
class m210504_175218_change_rate_type_session_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%session_rating}}','rate',$this->decimal(10,1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%session_rating}}','rate',$this->smallInteger());

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210504_175218_change_rate_type_session_rating cannot be reverted.\n";

        return false;
    }
    */
}
