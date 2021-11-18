<?php

use common\models\Program;
use common\models\User;
use yii\db\Migration;

/**
 * Class m201105_135251_seed_program
 */
class m201105_135251_seed_program extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            Program::tableName(),
            [
                'program_uuid' => Program::MENTOR_UUID,
                'name' => 'Программа менторства',
                'description' => 'Программа менторства',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ]
        );
        $this->insert(
            Program::tableName(),
            [
                'program_uuid' => Program::COACH_UUID,
                'name' => 'Программа коучинга',
                'description' => 'Программа коучинга',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201105_135251_seed_program cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201105_135251_seed_program cannot be reverted.\n";

        return false;
    }
    */
}
