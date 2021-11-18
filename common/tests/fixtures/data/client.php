<?php

declare(strict_types=1);

use common\models\Client;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\UserFixture;

return [
    'test-company-1' => [
        'name' => 'Тестовая компания 1',
        'status' => Client::STATUS_ACTIVE,
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id'],
        'logo' => 'src',
        'client_uuid' => ClientFixture::CLIENT_1_UUID
    ],
    'test-company-2' => [
        'name' => 'Тестовая компания 2',
        'status' => Client::STATUS_ACTIVE,
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_2['id'],
        'logo' => 'src',
        'client_uuid' => ClientFixture::CLIENT_2_UUID
    ],
    'test-company-3' => [
        'name' => 'Тестовая компания 3',
        'status' => Client::STATUS_ACTIVE,
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_3['id'],
        'logo' => 'src',
        'client_uuid' => ClientFixture::CLIENT_3_UUID
    ],
    'test-company-4' => [
        'name' => 'Тестовая компания 4',
        'status' => Client::STATUS_ACTIVE,
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id'],
        'logo' => 'src',
        'client_uuid' => ClientFixture::CLIENT_4_UUID
    ],
];
