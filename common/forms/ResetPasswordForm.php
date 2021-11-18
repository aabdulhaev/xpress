<?php
namespace common\forms;

use common\models\User;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    public $token;

    public function __construct(String $token, $config = [])
    {
        parent::__construct($config);
        $this->token = $token;
    }

    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'filter', 'filter' => 'trim'],
            ['password', 'string', 'min' => 8],
            ['token', 'validateToken'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'password' => 'Пароль'
        ];
    }

    public function validateToken($attribute): void
    {
        $token = $this->{$attribute};
        if (empty($token) || !is_string($token)) {
            $this->addError('password', 'Токен сброса пароля не может быть пустым');
        }
        if (!User::findByPasswordResetToken($token)) {
            $this->addError('password', "Токен {$token} сброса пароля не найден");
        }
    }
}
