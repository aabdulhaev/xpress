<?php

namespace common\tests\fixtures;

use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use yii\test\ActiveFixture;

class UserTrainingFixture extends ActiveFixture{

    public $modelClass = UserTraining::class;

    public $depends = [
        UserFixture::class,
        TrainingSessionFixture::class
    ];
}
