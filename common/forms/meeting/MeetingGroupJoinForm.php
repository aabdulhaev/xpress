<?php

declare(strict_types=1);

namespace common\forms\meeting;

use common\models\Meeting;
use common\models\User;
use common\validators\UuidValidator;
use yii\base\Model;

/**
 * MeetingGroupJoinForm form
 *
 * @SWG\Definition(required={"meeting_uuid"})
 *
 * @SWG\Property(property="meeting_uuid", type="string")
 * @SWG\Property(property="user_uuid", type="string")
 * @SWG\Property(property="token", type="string")
 */
class MeetingGroupJoinForm extends Model
{
    public $meeting_uuid;
    public $user_uuid;
    public $token;

    public function rules(): array
    {
        return [
            [['meeting_uuid'], 'required'],
            [['meeting_uuid', 'user_uuid', 'token'], 'string'],

            ['user_uuid', UuidValidator::class],
            [
                'user_uuid',
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'user_uuid'
            ],
            ['meeting_uuid', UuidValidator::class],
            [
                'meeting_uuid',
                'exist',
                'targetClass' => Meeting::class,
                'targetAttribute' => 'meeting_uuid'
            ],
        ];
    }
}
