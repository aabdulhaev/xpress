<?php

namespace common\tests\fixtures;

use common\models\TrainingSession;
use yii\test\ActiveFixture;

class TrainingSessionFixture extends ActiveFixture
{

    public $modelClass = TrainingSession::class;

    public $depends = [
        AuthAssignmentFixture::class,
        ClientFixture::class,
        UserFixture::class
    ];

    public const TRAINING_UUID_1 = '1eb2fe05-3419-69c0-ca20-b324c5e68f57';
    public const TRAINING_UUID_2 = '1eb2fe05-341c-60d0-59be-68ba20b2d189';
    public const TRAINING_UUID_3 = '1eb2fe05-341c-60d1-21f3-ccb0c31152e5';
    public const TRAINING_UUID_4 = '1eb2fe05-341e-67e0-6360-8f371b3350fb';
    public const TRAINING_UUID_5 = '1eb2fe05-341e-67e1-a1ae-32c04af60148';
    public const TRAINING_UUID_6 = '1eb2fe05-341e-67e2-f48e-eae4874c327e';
    public const TRAINING_UUID_7 = '1eb2fe05-341e-67e3-ce35-b3569bb00a49';
    public const TRAINING_UUID_8 = '1eb2fe05-341e-67e4-03bb-6640c306e72f';
    public const TRAINING_UUID_9 = '1eb2fe05-341e-67e5-e1ae-3fad7ac2ca7b';
    public const TRAINING_UUID_10 = '1eb2fe05-341e-67e6-e1d5-c51139061627';
    public const TRAINING_UUID_11 = '1ebe3d6c-da63-6b9e-9286-0242c0a8e003';
    public const TRAINING_UUID_12 = '1ebe3d6c-db57-60d2-8460-0242c0a8e003';
    public const TRAINING_UUID_13 = '1ebe3d6c-db57-65dc-a8c8-0242c0a8e003';
    public const TRAINING_UUID_14 = '1ebe3d6c-db57-671c-b646-0242c0a8e003';
    public const TRAINING_UUID_15 = '1ebe3d6c-db57-67da-8802-0242c0a8e003';
}
