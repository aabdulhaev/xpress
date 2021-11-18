<?php

declare(strict_types=1);
namespace common\tests\fixtures;

use common\models\UserProgram;
use yii\test\ActiveFixture;

class UserProgramFixture extends ActiveFixture {

    public $modelClass = UserProgram::class;

    public $depends = [
        UserFixture::class,
        ProgramFixture::class,
    ];
}
