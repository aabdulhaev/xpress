<?php

declare(strict_types=1);

namespace common\forms\training;

use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use common\validators\DateTimeValidator;
use yii\base\Model;

class TrainingMemberForm extends Model{

    public $link;
    public $status;
    public $training;
    public $user;

    public function __construct(User $user, TrainingSession $training, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
        $this->training = $training;
    }

    public function rules() : array
    {
        return [
            ['link', 'url', 'defaultScheme' => 'https'],
            ['status', 'in', 'range' => array_keys(UserTraining::statuses())]
        ];
    }

    public function attributeLabels() :array
    {
        return [
            'link' => 'Ссылка на конференцию',
            'status' => 'Статус'
        ];
    }
}
