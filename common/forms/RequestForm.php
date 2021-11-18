<?php

declare(strict_types=1);

namespace common\forms;

use common\models\Client;
use common\models\Request;
use common\models\TariffPlan;
use yii\base\Model;

class RequestForm extends Model{

    public $name;
    public $email;
    public $phone;
    public $description;

    public $type;

    public function __construct($type = Request::TYPE_CLIENT, $config = [])
    {
        parent::__construct($config);
        $this->type = $type;
    }


    public function rules() : array
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 12],
            [['email'], 'email'],
            [['description'], 'string', 'max' => 2048],
            [['description'], 'url', 'defaultScheme' => 'https', 'when' => function(){
                return $this->type === Request::TYPE_COACH;
            }]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'phone' => 'Номер телефона',
            'email' => 'Email',
            'description' => $this->type === Request::TYPE_COACH ? 'Ссылка на соц. сети':'Компания',
        ];
    }
}
