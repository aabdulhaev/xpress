<?php

use yii\db\Migration;

/**
 * Class m210308_203502_add_fk_training_rating
 */
class m210308_203502_add_fk_training_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk_training_rating',
           '{{%session_rating}}',
            'training_uuid',
            '{{%training_session}}',
            'training_uuid',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210308_203502_add_fk_training_rating cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210308_203502_add_fk_training_rating cannot be reverted.\n";

        return false;
    }
    */
}
