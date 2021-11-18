<?php

declare(strict_types=1);

namespace common\forms\user;

use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserTrainingCreateForm extends Model{

    public $user_uuid;
    public $status;
    public $comment;

    public function __construct(string $user_uuid, $config = [])
    {
        parent::__construct($config);
        $this->user_uuid = $user_uuid;
    }

    public function rules() : array
    {
        return [
            [['status', 'user_uuid'], 'required'],
            ['comment', 'string'],
            [
                'status',
                'in',
                'range' => array_keys(UserTraining::statuses())
            ],
            [
                'user_uuid',
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'user_uuid'
            ],
            [
                'training_uuid',
                'exist',
                'targetClass' => TrainingSession::class,
                'targetAttribute' => 'training_uuid'
            ]
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'assignment' => [
                    'status', 'user_uuid', 'comment'
                ]
            ]
        );
    }


}
