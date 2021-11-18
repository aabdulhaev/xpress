<?php

declare(strict_types=1);

namespace common\forms\training;

use common\models\Subject;
use common\models\TrainingSession;
use common\models\User;
use common\validators\DateTimeValidator;
use yii\base\Model;

class TrainingCreateForm extends Model
{

    public $owner_uuid;
    public $invited_uuid;
    public $start_at;
    public $subject_uuid;
    public $duration;
    public $user;
    public $moved_from;
    public $comment;
    public $moved_by_role;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
        $this->owner_uuid = $user->user_uuid;
    }

    public function rules(): array
    {
        return [
            [['start_at','duration'],'required'],
            ['start_at', DateTimeValidator::class],
            ['duration','integer', 'min' => 60 * 5, 'max' => 360 * 600],

            ['owner_uuid', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'user_uuid'],
            ['subject_uuid', 'exist', 'targetClass' => Subject::class, 'targetAttribute' => 'subject_uuid'],
            ['moved_from', 'exist', 'targetClass' => TrainingSession::class, 'targetAttribute' => 'training_uuid'],

            ['invited_uuid', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'user_uuid'],
            [
                'invited_uuid',
                'required',
                'when' => function (self $model) {
                    return $model->scenario === 'default';
                },
                'message' => 'Необходимо выбрать участника сессии'
            ],

            ['comment', 'required', 'when' => function (self $model) {
                return $model->moved_from;
            }],
            ['comment','string','max' => 1024],
            ['moved_by_role', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'invited_uuid' => 'Приглашаемый'
        ];
    }

    public function scenarios(): array
    {
        return array_merge(
            [
                parent::SCENARIO_DEFAULT => [
                    'start_at',
                    'duration',
                    'owner_uuid',
                    'invited_uuid',
                    'subject_uuid',
                ],
                'free' => [
                    'start_at',
                    'duration',
                    'owner_uuid',
                    'invited_uuid',
                    'subject_uuid',
                    'user',
                ],
                'move' => [
                    'start_at',
                    'duration',
                    'owner_uuid',
                    'invited_uuid',
                    'subject_uuid',
                    'comment',
                    'moved_from'
                ]
            ]
        );
    }
}
