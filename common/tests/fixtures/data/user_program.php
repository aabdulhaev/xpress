<?php

declare(strict_types=1);

use common\tests\fixtures\ProgramFixture;
use common\tests\fixtures\UserFixture;

$coaches = [];
$mentors = [];
$employees = [];
$constants = (new ReflectionClass(new UserFixture()))->getConstants();
for ($i = 1; $i <= 25; $i++) {
    $coaches['coach_' . $i . '_coach'] = [
        'program_uuid' => ProgramFixture::COACH_UUID,
        'user_uuid' => $constants['COACH_AUTH_' . $i]['id'],
        'created_by' => $constants['HR_AUTH_1']['id'],
        'created_at' => time()
    ];
    $mentors['ment_' . $i . '_mentor'] = [
        'program_uuid' => ProgramFixture::MENTOR_UUID,
        'user_uuid' => $constants['MENT_AUTH_' . $i]['id'],
        'created_by' => $constants['HR_AUTH_1']['id'],
        'created_at' => time()
    ];
    if ($i <= 3) {
        $employees['emp_' . $i . '_mentor'] = [
            'program_uuid' => ProgramFixture::MENTOR_UUID,
            'user_uuid' => $constants['EMP_AUTH_' . $i]['id'],
            'session_planed' => 10,
            'session_complete' => 0,
            'created_by' => $constants['HR_AUTH_1']['id'],
            'created_at' => time()
        ];

        if ($i === 3) {
            $employees['emp_' . $i . '_coach'] = [
                'program_uuid' => ProgramFixture::COACH_UUID,
                'user_uuid' => $constants['EMP_AUTH_' . $i]['id'],
                'session_planed' => 10,
                'session_complete' => 10,
                'created_by' => $constants['HR_AUTH_1']['id'],
                'created_at' => time()
            ];
        } else {
            $employees['emp_' . $i . '_coach'] = [
                'program_uuid' => ProgramFixture::COACH_UUID,
                'user_uuid' => $constants['EMP_AUTH_' . $i]['id'],
                'session_planed' => 10,
                'session_complete' => 0,
                'created_by' => $constants['HR_AUTH_1']['id'],
                'created_at' => time()
            ];
        }
    } elseif ($i <= 15) {
        $employees['emp_' . $i . '_mentor'] = [
            'program_uuid' => ProgramFixture::MENTOR_UUID,
            'user_uuid' => $constants['EMP_AUTH_' . $i]['id'],
            'session_planed' => 10,
            'session_complete' => 0,
            'created_by' => $constants['HR_AUTH_1']['id'],
            'created_at' => time()
        ];
    } else {
        $employees['emp_' . $i . '_coach'] = [
            'program_uuid' => ProgramFixture::COACH_UUID,
            'user_uuid' => $constants['EMP_AUTH_' . $i]['id'],
            'session_planed' => 10,
            'session_complete' => 0,
            'created_by' => $constants['HR_AUTH_1']['id'],
            'created_at' => time()
        ];
    }
}

return array_merge($coaches, $mentors, $employees);
