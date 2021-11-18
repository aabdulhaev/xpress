<?php

declare(strict_types=1);


use common\tests\fixtures\CompetenceFixture;
use common\tests\fixtures\UserFixture;

return [
    [
        'competence_uuid' => CompetenceFixture::COMP_1_UUID,
        'title' => 'Личное развитие',
        'description' => 'Личное развитие',
        'img_name' => 'personal_development',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_2_UUID,
        'title' => 'Эффективность',
        'description' => 'Эффективность',
        'img_name' => 'efficiency',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_3_UUID,
        'title' => 'Постановка целей',
        'description' => 'Постановка целей',
        'img_name' => 'goals',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_4_UUID,
        'title' => 'Осознанность',
        'description' => 'Осознанность',
        'img_name' => 'mindfulness',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_5_UUID,
        'title' => 'Здоровье и благополучие',
        'description' => 'Здоровье и благополучие',
        'img_name' => 'health',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_6_UUID,
        'title' => 'Лидерство',
        'description' => 'Лидерство',
        'img_name' => 'leadership',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_7_UUID,
        'title' => 'Управление командой',
        'description' => 'Управление командой',
        'img_name' => 'team_management',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_8_UUID,
        'title' => 'Развивающий стиль руководства',
        'description' => 'Развивающий стиль руководства',
        'img_name' => 'up_management',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_9_UUID,
        'title' => 'Коммуникация',
        'description' => 'Коммуникация',
        'img_name' => 'communication',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_10_UUID,
        'title' => 'Работа с конфликтами',
        'description' => 'Работа с конфликтами',
        'img_name' => 'working_with_conflicts',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    [
        'competence_uuid' => CompetenceFixture::COMP_11_UUID,
        'title' => 'Управление эмоциями',
        'description' => 'Управление эмоциями',
        'img_name' => 'emotions',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ]
];
