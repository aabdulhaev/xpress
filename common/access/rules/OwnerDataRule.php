<?php


namespace common\access\rules;
use yii\rbac\Rule;

class OwnerDataRule extends Rule
{
    public $name = 'ounData';

    public function execute($user_uuid, $item, $params)
    {
        return $user_uuid === $params['user_uuid'];
    }
}
