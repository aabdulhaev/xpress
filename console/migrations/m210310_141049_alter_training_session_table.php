<?php

use yii\db\Migration;

/**
 * Class m210310_141049_alter_training_session_table
 */
class m210310_141049_alter_training_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%training_session}}','program_uuid','UUID');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210310_141049_alter_training_session_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210310_141049_alter_training_session_table cannot be reverted.\n";

        return false;
    }
    */
}
