<?php

declare(strict_types=1);

namespace common\forms;

use common\models\EmployeeMentor;
use common\models\User;
use common\validators\UniqueValidator;
use yii\base\Model;

class CreateConnectForm extends Model
{

    public $employee_uuid;
    public $mentor_uuid;


    public function rules(): array
    {
        return [
            ['mentor_uuid', 'required'],
            ['mentor_uuid', 'string', 'max' => 36],
            ['mentor_uuid', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'user_uuid'],

            ['employee_uuid', 'required'],
            ['employee_uuid', 'string', 'max' => 36],
            ['employee_uuid', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'user_uuid'],

            [
                ['employee_uuid'],
                UniqueValidator::class,
                'targetClass' => EmployeeMentor::class,
                'targetAttribute' => ['employee_uuid', 'mentor_uuid']
            ]
        ];
    }
}
