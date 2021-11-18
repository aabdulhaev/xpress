<?php

declare(strict_types=1);


use common\models\Section;
use common\tests\fixtures\UserFixture;

return [
    [
        'section_uuid' => Section::SECTION_LIBRARY_UUID,
        'title' => 'Библиотека знаний',
        'description' => 'Библиотека знаний',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'section_uuid' => Section::SECTION_WEBINAR_UUID,
        'title' => 'Вебинары',
        'description' => 'Вебинары',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'section_uuid' => Section::SECTION_POLL_UUID,
        'title' => 'Опросы',
        'description' => 'Опросы',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
];
