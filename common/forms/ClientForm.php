<?php

declare(strict_types=1);

namespace common\forms;

use common\access\Rbac;
use common\models\Client;
use common\models\Program;
use common\models\TariffPlan;
use common\models\User;
use yii\base\Model;

class ClientForm extends Model{

    public $name;
    public $programs = [];
    public $coaches = [];
    public $tariff_uuid;

    public $client;

    public function __construct(Client $client = null, $config = [])
    {
        if($client){
            $this->name = $client->name;
            $this->client = $client;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['name'], 'string', 'min' => '2', 'max' => 64],

            ['programs', 'required'],
            ['programs', 'each', 'rule' => ['programValidator'], 'skipOnEmpty' => true],

            ['coaches', 'each', 'rule' => ['coachValidator'], 'skipOnEmpty' => true],

            ['tariff_uuid', 'default', 'value' => TariffPlan::SEED_TARIFF_UUID]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'programs' => 'Программы',
            'coaches' => 'Коучи',
            'tariff_uuid' => 'Тариф',
        ];
    }

    public function programValidator($attribute)
    {
        $errors = [];
        foreach ($this->{$attribute} as $idx => $program_uuid) {
            if (!Program::find()
                    ->andWhere([
                        'program_uuid' => $program_uuid
                    ])->exists()) {
                $errors['program_'.$idx] = $program_uuid . ' не существует';
            }
        }

        if (count($errors) > 0){
            $this->addErrors($errors);
        }
    }

    public function coachValidator($attribute)
    {
        $errors = [];
        foreach ($this->{$attribute} as $idx => $coach_uuid) {
            $coach = User::find()
                ->andWhere([
                    'user_uuid' => $coach_uuid,
                    'role' => Rbac::ROLE_COACH
                ])
                ->andWhere([
                    '>',
                    'status',
                    User::STATUS_INACTIVE
                ])->exists();
            if (!$coach) {
                $errors['coach_'.$idx] = $coach_uuid . ' не существует или он не активен.';
            }
        }

        if (count($errors) > 0){
            $this->addErrors($errors);
        }
    }

}
