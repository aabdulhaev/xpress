<?php

namespace common\tests\fixtures;

use common\models\SessionRating;
use yii\test\ActiveFixture;

class TrainingRatingFixture extends ActiveFixture
{

    public $modelClass = SessionRating::class;
    public $depends = [
        AuthAssignmentFixture::class,
        ClientFixture::class,
        UserFixture::class,
        TrainingSessionFixture::class
    ];
}
