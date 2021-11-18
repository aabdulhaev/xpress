<?php

declare(strict_types=1);

use common\models\UserTraining;
use common\tests\fixtures\TrainingSessionFixture;
use common\tests\fixtures\UserFixture;



return [
    /**
     * свободный слот созданный коучем
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_1,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * свободный слот созданный коучем
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_13,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от коуча
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_2,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_2,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия подтверждена т.е в расписании
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_3,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_3,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия отменена
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_4,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_CANCEL,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_4,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_CANCEL,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия завершена и требует оценки от сотрудника
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_5,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_NOT_ESTIMATE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_5,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_ESTIMATE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия завершена
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_6,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_ESTIMATE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_6,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_ESTIMATE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от сотрудника
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_7,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_7,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    /**
     * сессия завершена но требует оценки от коуча
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_8,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_ESTIMATE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_8,
        'user_uuid' => UserFixture::COACH_AUTH_1['id'],
        'status' => UserTraining::STATUS_NOT_ESTIMATE,
        'created_at' => time(),
        'created_by' => UserFixture::COACH_AUTH_1['id']
    ],



    /**
     * свободный слот созданный ментором
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_9,
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от ментора
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_10,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_10,
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'status' => UserTraining::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    /**
     * сессия подтверждена т.е в расписании
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_11,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_11,
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    /**
     * сессия ожидает подтверждения от сотрудника
     */
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_12,
        'user_uuid' => UserFixture::EMP_AUTH_1['id'],
        'status' => UserTraining::STATUS_NOT_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
    [
        'training_uuid' => TrainingSessionFixture::TRAINING_UUID_12,
        'user_uuid' => UserFixture::MENT_AUTH_1['id'],
        'status' => UserTraining::STATUS_CONFIRM,
        'created_at' => time(),
        'created_by' => UserFixture::MENT_AUTH_1['id']
    ],
];
