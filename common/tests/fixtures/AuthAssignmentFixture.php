<?php

namespace common\tests\fixtures;

use yii\test\ActiveFixture;

class AuthAssignmentFixture extends ActiveFixture
{
    public $tableName = 'auth_assignment';

    public $depends = [
        AuthItemFixture::class,
    ];
}
