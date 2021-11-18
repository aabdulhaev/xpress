<?php

namespace common\tests\fixtures;

use common\models\EmployeeMentor;
use yii\test\ActiveFixture;

class EmployeeMentorFixture extends ActiveFixture
{

    public $modelClass = EmployeeMentor::class;
    public $depends = [
        UserFixture::class,
    ];

}
