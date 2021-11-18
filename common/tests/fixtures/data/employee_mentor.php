<?php

declare(strict_types=1);

use common\models\EmployeeMentor;
use common\tests\fixtures\UserFixture;

return [
    /**
     * Привязки сотрудников в коучам
     */
    'emp_coach_1' => [
        'employee_uuid' => UserFixture::EMP_AUTH_1['id'],
        'mentor_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => EmployeeMentor::STATUS_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_1['id']
    ],
    'emp_coach_2' => [
        'employee_uuid' => UserFixture::EMP_AUTH_1['id'],
        'mentor_uuid' => UserFixture::COACH_AUTH_2['id'],
        'status' => EmployeeMentor::STATUS_DECLINE,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_1['id']
    ],
    'emp_coach_3' => [
        'employee_uuid' => UserFixture::EMP_AUTH_1['id'],
        'mentor_uuid' => UserFixture::COACH_AUTH_3['id'],
        'status' => EmployeeMentor::STATUS_NOT_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_4['id']
    ],
    'emp_coach_4' => [
        'employee_uuid' => UserFixture::EMP_AUTH_2['id'],
        'mentor_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => EmployeeMentor::STATUS_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_4['id']
    ],
    'emp_coach_5' => [
        'employee_uuid' => UserFixture::EMP_AUTH_2['id'],
        'mentor_uuid' => UserFixture::COACH_AUTH_2['id'],
        'status' => EmployeeMentor::STATUS_DECLINE,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_4['id']
    ],
    'emp_coach_6' => [
        'employee_uuid' => UserFixture::EMP_AUTH_3['id'],
        'mentor_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => EmployeeMentor::STATUS_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_4['id']
    ],

    /**
     * привязки сотрудников к менторам
     */
    'emp_mentor_1' => [
        'employee_uuid' => UserFixture::EMP_AUTH_1['id'],
        'mentor_uuid' => UserFixture::MENT_AUTH_1['id'],
        'status' => EmployeeMentor::STATUS_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_1['id']
    ],
    'emp_mentor_2' => [
        'employee_uuid' => UserFixture::EMP_AUTH_1['id'],
        'mentor_uuid' => UserFixture::MENT_AUTH_2['id'],
        'status' => EmployeeMentor::STATUS_NOT_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_1['id']
    ],
    'emp_mentor_3' => [
        'employee_uuid' => UserFixture::EMP_AUTH_1['id'],
        'mentor_uuid' => UserFixture::MENT_AUTH_3['id'],
        'status' => EmployeeMentor::STATUS_DECLINE,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_1['id']
    ],
    'emp_mentor_4' => [
        'employee_uuid' => UserFixture::EMP_AUTH_2['id'],
        'mentor_uuid' => UserFixture::MENT_AUTH_1['id'],
        'status' => EmployeeMentor::STATUS_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_1['id']
    ],
    'emp_mentor_5' => [
        'employee_uuid' => UserFixture::EMP_AUTH_2['id'],
        'mentor_uuid' => UserFixture::MENT_AUTH_2['id'],
        'status' => EmployeeMentor::STATUS_APPROVED,
        'created_at' => time(),
        'created_by' => UserFixture::HR_AUTH_4['id']
    ],
];
