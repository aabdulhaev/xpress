<?php

namespace common\tests\fixtures;

use common\models\Request;
use yii\test\ActiveFixture;

class RequestFixture extends ActiveFixture
{
    public const CLIENT_UUID = '1eb178ee-dccd-60f1-b11a-d0a8f7b46528';
    public const COACH_UUID = '1eb178ee-dccd-60f0-f2dd-84e32da07272';

    public $modelClass = Request::class;
    public $depends = [
        AuthAssignmentFixture::class,
        UserFixture::class,
    ];
}
