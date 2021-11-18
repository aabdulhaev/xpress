<?php

declare(strict_types=1);

namespace common\forms;

use common\models\User;
use yii\base\Model;

/**
 * @OA\Schema(
 *     schema="LoginForm",
 *     type="object",
 *     required={"password", "login"},
 * )
 */
class LoginForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $login;

    /**
     * @OA\Property()
     * @var string
     */
    public $password;

    protected $user;


    public function rules(): array
    {
        return [
            ['login', 'trim'],
            ['login', 'required'],
            ['login', 'email'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 32],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'login' => 'E-mail',
            'password' => 'Пароль',
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный E-mail или пароль');
            }
        }
    }

    protected function getUser()
    {
        if ($this->user === null) {
            $this->user = User::findActiveIdentityByEmail($this->login);
        }

        return $this->user;
    }

    public function login(): ?string
    {
        if ($this->validate()) {
            return $this->user->getLongToken();
        }

        return null;
    }
}
