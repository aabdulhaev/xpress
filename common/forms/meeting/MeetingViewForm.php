<?php

declare(strict_types=1);

namespace common\forms\meeting;

use common\models\Meeting;
use common\models\User;
use yii\base\Model;

/**
 * MeetingGroupJoinForm form
 *
 * @SWG\Definition(required={"meeting_uuid"})
 *
 * @SWG\Property(property="meeting_uuid", type="string")
 */
class MeetingViewForm extends Model
{
    public $meeting_uuid;

    public function rules(): array
    {
        return [
            [['meeting_uuid'], 'required'],
            [['meeting_uuid'], 'string'],
            [
                'meeting_uuid',
                'exist',
                'targetClass' => Meeting::class,
                'targetAttribute' => 'meeting_uuid'
            ],
        ];
    }
}
