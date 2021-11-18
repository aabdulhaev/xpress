<?php

namespace api\modules\v1\forms\user;

use yii\base\Model;
use common\models\User;

/**
 * Class UserUpdatePasswordForm
 * @package api\modules\v1\forms\user
 */
class UserUpdatePasswordForm extends Model
{
    public $user_id;
    public $old_password;
    public $new_password;

    public function rules()
    {
        return [
            ['old_password', 'required'],
            ['old_password', 'string', 'length' => [6, 16]],
            ['old_password', 'check'],

            ['new_password', 'required'],
            ['new_password', 'string', 'length' => [6, 16]],
            ['new_password', 'checkSpace'],
        ];
    }

    public function check()
    {
        $user = User::findOne($this->user_id);
        if (!$user->validatePassword($this->old_password)) {
            $this->addError('old_password', 'Текущий пароль указан неверно.');
        }
    }

    public function checkSpace()
    {
        if (strpos($this->new_password, ' ') !== false) {
            $this->addError('new_password', 'Пароль не должен содержать пробелов.');
        }
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'old_password' => 'Старый пароль',
            'new_password' => 'Новый пароль',
        ];
    }
}
