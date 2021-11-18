<?php

use yii\db\Migration;

/**
 * Class m210219_113951_alter_training_session_table
 */
class m210219_113951_alter_training_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%training_session}}', 'subject_uuid');
        $this->addColumn('{{%training_session}}', 'subject_uuid', 'UUID');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210219_113951_alter_training_session_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210219_113951_alter_training_session_table cannot be reverted.\n";

        return false;
    }
    */
}
