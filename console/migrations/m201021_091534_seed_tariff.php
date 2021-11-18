<?php

use common\models\TariffPlan;
use common\models\User;
use yii\db\Migration;

/**
 * Class m201021_091534_seed_tariff
 */
class m201021_091534_seed_tariff extends Migration
{
    private $tablename = '{{%tariff_plan}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            $this->tablename,
            [
                'name' => 'Демо тариф',
                'description' => 'Демо тариф на период разработки.',
                'cost' => 0.00,
                'created_by' => User::SEED_ADMIN_UUID,
                'created_at' => time(),
                'tariff_uuid' => TariffPlan::SEED_TARIFF_UUID,
                'constraints' => [
                    'mentor' => 1000,
                    'coach' => 1000,
                    'employee' => 1000,
                    'expired' => 60 * 60 * 24 * 30 * 12
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(
            $this->tablename,
            ['tariff_uuid' => TariffPlan::SEED_TARIFF_UUID]
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_091534_seed_tariff cannot be reverted.\n";

        return false;
    }
    */
}
