<?php

namespace api\components;

use common\models\User as UserModel;

/**
 * Class User
 * @package api\components
 *
 * @property UserModel $identity
 */
class User extends \yii\web\User
{

    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if (!in_array($permissionName, ['?', '@']) && $this->identity->status != UserModel::STATUS_ACTIVE) {
            return false;
        }
        return parent::can($permissionName, $params, $allowCaching);
    }

}
