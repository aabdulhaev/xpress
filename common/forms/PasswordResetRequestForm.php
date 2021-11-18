<?php
namespace common\forms;

use yii\base\Model;
use common\models\User;
use yii\db\Query;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => function($query){$query->where(['~*',User::tableName().'.[[email]]',$this->email])->andWhere(['!=', User::tableName().'.[[status]]', User::STATUS_SUSPENDED]);},
                'message' => 'Пользователь с таким Email не найден'
            ],
        ];
    }
}
