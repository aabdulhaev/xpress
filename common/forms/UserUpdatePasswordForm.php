<?php

namespace common\forms;

use Yii;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class UserUpdatePasswordForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $password;
    /**
     * @OA\Property()
     * @var string
     */
    public $old_password;

    public function rules(): array
    {
        return [
            ['password', 'required'],
            ['password', 'filter', 'filter' => 'trim'],
            ['password', 'string', 'min' => 8],
            ['old_password', 'required'],
            ['old_password', 'validatePassword']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'password' => 'Новый пароль',
            'old_password' => 'Старый пароль',
        ];
    }

    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->{$attribute})) {
                $this->addError($attribute, 'Неверный пароль');
            }
        }
    }

    protected function getUser()
    {
        return Yii::$app->user->identity;
    }
}