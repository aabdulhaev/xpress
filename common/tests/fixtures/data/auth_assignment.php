<?php

use common\access\Rbac;
use common\tests\fixtures\UserFixture;

$auth = [];
$constants = (new ReflectionClass(new UserFixture))->getConstants();
foreach ($constants as $key => $val) {
    if ($key === 'PASSWORD' || $key === 'PASSWORD_HASH') {
        continue;
    }

    $auth[] = [
        'item_name' => $val['r'],
        'user_id' => $val['id'],
    ];
}

return $auth;
