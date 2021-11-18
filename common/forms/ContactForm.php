<?php

declare(strict_types=1);

namespace common\forms;

use common\models\User;
use yii\base\Model;

class ContactForm extends Model{

    public $userFrom;
    public $userTo;
    public $body;
    public $template = 'default';

    public function __construct(User $userFrom, User $userTo, $template = null, $config = [])
    {
        $this->userFrom = $userFrom;
        $this->userTo = $userTo;

        if(!empty($template)){
            $this->template = $template;
        }

        parent::__construct($config);
    }

    public function rules() : array
    {
        return [
          ['body', 'string', 'max' => 5000,'skipOnEmpty'=>true],
          ['body', 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'body' => 'Сообщение'
        ];
    }
}
