<?php

declare(strict_types=1);

use common\models\ClientCoach;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\UserFixture;

$clientCoaches = [];
$constants = (new ReflectionClass(new UserFixture()))->getConstants();

for ($i = 1; $i <= 25; $i++) {
    $clientCoaches[] = [
        'client_uuid' => ClientFixture::CLIENT_1_UUID,
        'coach_uuid' => $constants['COACH_AUTH_' . $i]['id'],
        'created_at' => time(),
        'created_by' => $constants['HR_AUTH_1']['id'],
        'status' => ClientCoach::STATUS_APPROVED
    ];

    if ($i > 14 && $i <= 25) {
        $clientCoaches[] = [
            'client_uuid' => ClientFixture::CLIENT_2_UUID,
            'coach_uuid' => $constants['COACH_AUTH_' . $i]['id'],
            'created_at' => time(),
            'created_by' => $constants['HR_AUTH_2']['id'],
            'status' => ClientCoach::STATUS_APPROVED
        ];
    }
}

return $clientCoaches;
