<?php

declare(strict_types=1);

namespace common\forms;

use common\models\EmployeeMentor;
use common\models\User;
use yii\base\Model;

class CreateConnectRequestForm extends Model
{

    public $employee_uuid;
    public $mentor_uuid;
    public $employee;
    public $comment;

    public function __construct(User $employee, User $coach, $config = [])
    {
        $this->employee = $employee;
        $this->employee_uuid = $employee->user_uuid;
        $this->mentor_uuid = $coach->user_uuid;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['mentor_uuid', 'required'],
            ['mentor_uuid', 'string', 'max' => 36],
            ['mentor_uuid', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'user_uuid'],
            [
                ['employee_uuid', 'mentor_uuid'],
                'unique',
                'targetClass' => EmployeeMentor::class,
                'targetAttribute' => ['employee_uuid', 'mentor_uuid']
            ],
            ['comment', 'required'],
            ['comment','string','max' => 255],
            ['comment','filter','filter' => 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
          'comment' => 'Комментарий'
        ];
    }
}
