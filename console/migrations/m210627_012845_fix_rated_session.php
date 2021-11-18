<?php

use common\models\TrainingSession;
use yii\db\Migration;

/**
 * Class m210627_012845_fix_rated_session
 */
class m210627_012845_fix_rated_session extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand('
            UPDATE training_session
            SET status = '.TrainingSession::STATUS_RATED.'
            WHERE training_uuid IN (
                SELECT training_uuid
                FROM session_rating
                GROUP BY training_uuid
                HAVING COUNT(training_uuid) = 2
            ) AND status NOT IN (
                '.TrainingSession::STATUS_DELETED.',
                '.TrainingSession::STATUS_RATED.'
            )')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210627_012845_fix_rated_session cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210627_012845_fix_rated_session cannot be reverted.\n";

        return false;
    }
    */
}
