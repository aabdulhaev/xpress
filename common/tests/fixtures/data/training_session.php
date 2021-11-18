<?php

declare(strict_types=1);

use common\models\TrainingSession;
use common\tests\fixtures\SubjectFixture;
use common\tests\fixtures\TrainingSessionFixture;
use common\tests\fixtures\UserFixture;

date_default_timezone_set('UTC');

return [
    /**
     * свободный слот созданный коучем
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_1,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+1 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_FREE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от коуча
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_2,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+1 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::EMP_AUTH_1['id']
    ],
    /**
     * сессия подтверждена т.е в расписании
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_3,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+1 day 10 hour')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3800,
        'status' => TrainingSession::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия отменена
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_4,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+4 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_CANCEL,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия завершена вчера
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_5,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('-1 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_COMPLETED,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия завершена позавчера
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_6,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('-2 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_COMPLETED,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от сотрудника
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_7,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+1 day 2 hours')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия завершена в прошлом месяце
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_8,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('-1 month')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_COMPLETED,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],


    /**
     * свободный слот созданный ментором
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_9,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+1 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_FREE,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от ментора
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_10,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+2 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::EMP_AUTH_1['id']
    ],
    /**
     * сессия подтверждена т.е в расписании
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_11,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+3 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3800,
        'status' => TrainingSession::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от сотрудника
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_12,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('+1 day 2 hours')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],


    /**
     * свободный слот у которого просрочено время начала сессии созданный коучем
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_13,
        'subject_uuid' => SubjectFixture::SUB_1_UUID,
        'start_at_tc' => date_create('noon')
            ->modify('-1 day')
            ->format('Y-m-d H:i:sP'),
        'duration' => 3600,
        'status' => TrainingSession::STATUS_FREE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],

];
