<?php

declare(strict_types=1);
namespace common\tests\fixtures;

use common\models\Program;
use yii\test\ActiveFixture;

class ProgramFixture extends ActiveFixture {

    public const MENTOR_UUID = Program::MENTOR_UUID;
    public const COACH_UUID = Program::COACH_UUID;

    public $modelClass = Program::class;
}
