<?php

declare(strict_types=1);

use common\tests\fixtures\SubjectFixture;
use common\tests\fixtures\UserFixture;

return [
    'emp-sub-1' => [
        'user_uuid' => UserFixture::EMP_AUTH_2['id'],
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'created_at' => time(),
    ],
    'emp-sub-2' => [
        'user_uuid' => UserFixture::EMP_AUTH_2['id'],
        'subject_uuid' => SubjectFixture::SUB_3_UUID,
        'created_at' => time(),
    ],
    'coach-sub-1' => [
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'created_at' => time(),
    ],
    'coach-sub-2' => [
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'subject_uuid' => SubjectFixture::SUB_3_UUID,
        'created_at' => time(),
    ],
    'coach-sub-3' => [
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'subject_uuid' => SubjectFixture::SUB_4_UUID,
        'created_at' => time(),
    ],
    'mentor-sub-1' => [
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'created_at' => time(),
    ],
    'mentor-sub-2' => [
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'subject_uuid' => SubjectFixture::SUB_3_UUID,
        'created_at' => time(),
    ],
    'mentor-sub-3' => [
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'subject_uuid' => SubjectFixture::SUB_4_UUID,
        'created_at' => time(),
    ],
];
