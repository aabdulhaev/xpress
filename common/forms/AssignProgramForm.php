<?php

declare(strict_types=1);

namespace common\forms;

use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AssignProgramForm extends Model{

    public $programs;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->programs = ArrayHelper::getColumn($user->programAssignments, 'program_uuid');
    }

    public function rules(): array
    {
        return [
            ['programs', 'each', 'rule' => ['boolean']],
            ['programs', 'default', 'value' => []],
        ];
    }
}
