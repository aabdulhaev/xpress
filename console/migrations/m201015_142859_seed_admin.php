<?php

use common\access\Rbac;
use common\models\User;
use Ramsey\Uuid\Uuid;
use yii\db\Migration;

/**
 * Class m201015_142859_seed_admin
 */
class m201015_142859_seed_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%user}}',
            [
                'first_name' => 'Самый',
                'last_name' => 'Главный',
                'user_uuid' => User::SEED_ADMIN_UUID,
                'email' => 'admin@admin.xpress.loc',
                'password_hash' => Yii::$app->security->generatePasswordHash('123qwe'),
                'phone' => '+79999999999',
                'status' => User::STATUS_ACTIVE,
                'role' => Rbac::ROLE_ADMIN,
                'auth_key' => Yii::$app->security->generateRandomString(32),
                'created_at' => time(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201015_142859_seed_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_142859_seed_admin cannot be reverted.\n";

        return false;
    }
    */
}
