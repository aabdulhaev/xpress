<?php

declare(strict_types=1);

namespace common\forms;

use common\models\Client;
use common\models\ClientCoach;
use common\models\User;
use yii\base\Model;

class AssignCoachForm extends Model{

    public $client_uuid;
    public $coach_uuid;


    public function rules() : array
    {
        return [
            ['coach_uuid','required'],
            ['coach_uuid','string','max' => 36],
            ['coach_uuid','exist','targetClass' => User::class,'targetAttribute' => 'user_uuid'],

            ['client_uuid','required'],
            ['client_uuid','string','max' => 36],
            ['client_uuid','exist','targetClass' => Client::class,'targetAttribute' => 'client_uuid'],

            [['coach_uuid'],'unique', 'targetClass' => ClientCoach::class, 'targetAttribute' => ['client_uuid','coach_uuid']]
        ];
    }
}
