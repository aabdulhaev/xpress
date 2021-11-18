<?php

declare(strict_types=1);

namespace common\forms;

use common\models\User;
use yii\base\Model;

class InviteForm extends Model{

    public $userFrom;
    public $userTo;
    public $body;

    public function __construct(User $userFrom, User $userTo, $config = [])
    {
        $this->userFrom = $userFrom;
        $this->userTo = $userTo;
        parent::__construct($config);
    }

    public function rules() : array
    {
        return [
          ['body', 'string', 'min' => 25, 'max' => 5000],
          ['body', 'required'],
        ];
    }

    public function attributeLabels() : array
    {
        return [
            'body' => 'Сообщение'
        ];
    }
}
