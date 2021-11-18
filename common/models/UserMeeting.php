<?php

namespace common\models;


use Ramsey\Uuid\Uuid;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "meeting".
 *
 * @property string $user_meeting_uuid
 * @property string $meeting_uuid
 * @property string $user_uuid
 * @property string $email
 * @property integer $status
 * @property integer $token
 *
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $notify_status
 *
 * @property TrainingSession $training
 * @property Meeting $meeting
 * @property User $user
 */
class UserMeeting extends ActiveRecord
{
    const STATUS_NOT_INVITED = 0;
    const STATUS_CONFIRMED  = 1;
    const STATUS_JOINED = 2;
    const STATUS_DELETED = 10;

    public const NOTIFY_STATUS_NOT_SEND = 0;
    public const NOTIFY_STATUS_CONFIRM_SEND = 5;
    public const NOTIFY_STATUS_24_SEND = 10;
    public const NOTIFY_STATUS_START_SEND = 20;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_meeting';
    }

    public function behaviors(): array
    {
        return [
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    public function fields()
    {
        return [
            'user_meeting_uuid',
            'meeting_uuid',
            'user_uuid',
            'email'
        ];
    }

    /**
     * @param string $meetingUuid
     * @param string|null $userUuid
     * @param string|null $email
     * @param int $status
     * @return static
     * @throws \yii\base\Exception
     */
    public static function create(
        string $meetingUuid,
        string $userUuid = null,
        string $email = null,
        int $status = UserMeeting::STATUS_NOT_INVITED
    ): self
    {
        $model = new static();

        $model->user_meeting_uuid = Uuid::uuid6();
        $model->meeting_uuid = $meetingUuid;
        if (!empty($userUuid)) {
            $model->user_uuid = $userUuid;
        }
        if (!empty($email)) {
            $model->email = $email;
        }
        if (!empty($status)) {
            $model->status = $status;
        }
        $model->token = \Yii::$app->security->generateRandomString();

        return $model;
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_uuid' => 'user_uuid']);
    }

    public function getTraining(): ActiveQuery
    {
        return $this->hasOne(TrainingSession::class, ['training_uuid' => 'training_uuid']);
    }

    public function getMeeting(): ActiveQuery
    {
        return $this->hasOne(Meeting::class, ['meeting_uuid' => 'meeting_uuid']);
    }

    /**
     * @return bool
     */
    public function isNotInvited(): bool
    {
        return $this->status == self::STATUS_NOT_INVITED;
    }

    /**
     * @return bool
     */
    public function isJoined(): bool
    {
        return $this->status == self::STATUS_JOINED;
    }

    public function toJoined(): void
    {
        $this->status = self::STATUS_JOINED;
    }

    /**
     * @param int $status
     */
    public function setNotifyStatus(int $status): void
    {
        $this->notify_status = $status;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
