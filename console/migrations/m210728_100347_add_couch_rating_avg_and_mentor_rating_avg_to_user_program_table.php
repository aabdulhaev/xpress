<?php

use yii\db\Migration;

/**
 * Class m210728_100347_add_couch_rating_avg_and_mentor_rating_avg_to_user_program_table
 */
class m210728_100347_add_couch_rating_avg_and_mentor_rating_avg_to_user_program_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_program}}','couch_rating_avg', $this->decimal(10,1));
        $this->addColumn('{{%user_program}}','mentor_rating_avg', $this->decimal(10,1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_program}}','couch_rating_avg');
        $this->dropColumn('{{%user_program}}','mentor_rating_avg');
    }
}
