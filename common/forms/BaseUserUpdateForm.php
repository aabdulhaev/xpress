<?php

declare(strict_types=1);


namespace common\forms;


use common\models\User;
use yii\base\Model;

abstract class BaseUserUpdateForm extends Model
{
    public $status;
    public $user;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
        $this->status = $user->status;
    }

    public function rules(): array
    {
        return [
            ['status', 'required'],
            ['status', 'integer'],
            ['status', 'in', 'range' => array_keys(User::statuses())],
        ];
    }
}
