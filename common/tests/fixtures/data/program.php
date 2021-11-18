<?php

declare(strict_types=1);

use common\tests\fixtures\ProgramFixture;
use common\tests\fixtures\UserFixture;

return [
    'mentor' => [
        'program_uuid' => ProgramFixture::MENTOR_UUID,
        'name' => 'Программа менторства',
        'description' => 'Программа менторства',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ],
    'coach' => [
        'program_uuid' => ProgramFixture::COACH_UUID,
        'name' => 'Программа коучинга',
        'description' => 'Программа коучинга',
        'created_at' => time(),
        'created_by' => UserFixture::ADMIN_AUTH_1['id']
    ]
];
