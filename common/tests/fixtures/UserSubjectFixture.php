<?php

namespace common\tests\fixtures;

use common\models\UserSubject;
use yii\test\ActiveFixture;

class UserSubjectFixture extends ActiveFixture
{
    public $modelClass = UserSubject::class;
    public $depends = [
        UserFixture::class,
        SubjectFixture::class
    ];
}
