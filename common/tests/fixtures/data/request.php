<?php

declare(strict_types=1);

use common\models\Request;
use common\tests\fixtures\RequestFixture;

return [
    'coach' => [
        'name' => 'Тренер',
        'email' => 'coach@test.loc',
        'phone' => '9677580022',
        'description' => 'Тестовый запрос от тренера',
        'request_uuid' => RequestFixture::COACH_UUID,
        'status' => Request::STATUS_NEW,
        'type' => Request::TYPE_COACH,
        'created_at' => time()
    ],
    'client' => [
        'name' => 'Клиент',
        'email' => 'client@test.loc',
        'phone' => '9677580033',
        'description' => 'Тестовый запрос от клиента',
        'request_uuid' => RequestFixture::CLIENT_UUID,
        'status' => Request::STATUS_NEW,
        'type' => Request::TYPE_CLIENT,
        'created_at' => time()
    ]
];
