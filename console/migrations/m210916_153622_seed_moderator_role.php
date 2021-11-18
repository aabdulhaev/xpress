<?php

use common\access\Rbac;
use yii\db\Migration;

/**
 * Class m210916_153622_seed_moderator_role
 */
class m210916_153622_seed_moderator_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        if (!$auth->getRole(Rbac::ROLE_MODERATOR)) {
            $admin = $auth->getRole(Rbac::ROLE_ADMIN);
            $moderator = $auth->createRole(Rbac::ROLE_MODERATOR);
            $auth->add($moderator);
            $auth->addChild($admin, $moderator);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if ($auth->getRole(Rbac::ROLE_MODERATOR)) {
            $admin = $auth->getRole(Rbac::ROLE_ADMIN);
            $moderator = $auth->getRole(Rbac::ROLE_MODERATOR);
            $auth->removeChild($admin, $moderator);
            $auth->remove($moderator);
        }
    }
}
