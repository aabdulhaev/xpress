<?php

namespace common\tests\fixtures;

use common\models\ClientCoach;
use yii\test\ActiveFixture;

class ClientCoachFixture extends ActiveFixture
{

    public $modelClass = ClientCoach::class;

    public $depends = [
        UserFixture::class,
        ClientFixture::class,
        AuthAssignmentFixture::class
    ];

}
