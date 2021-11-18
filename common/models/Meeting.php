<?php

namespace common\models;

use common\access\Rbac;
use common\models\events\AdminCreateGroupMeeting;
use common\models\events\CancelMeetingNotification;
use common\models\events\MentorCreateMeeting;
use common\models\events\MoveMeetingNotification;
use common\models\traits\AggregateRoot;
use common\models\traits\EventTrait;
use DateTime;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * @OA\Schema()
 *
 * @OA\Property (property="meeting_uuid", type="string")
 * @OA\Property (property="training_uuid", type="string")
 * @OA\Property (property="start_at", type="string")
 * @OA\Property (property="end_at", type="string")
 * @OA\Property (property="status", type="integer", enum={1, 10, 20})
 * @OA\Property (property="title", type="string")
 * @OA\Property (property="description", type="string")
 * @OA\Property (property="type", type="integer", enum={0, 1})
 *
 * This is the model class for table "meeting".
 *
 * @property string $meeting_uuid
 * @property string $training_uuid
 * @property string $start_at
 * @property string $end_at
 * @property int $status
 * @property string $title
 * @property string $description
 * @property string $group_link
 * @property integer $type
 *
 * @property int $created_at
 * @property int|null $updated_at
 * @property string $created_by
 * @property string|null $updated_by
 *
 * @property TrainingSession $training
 * @property UserMeeting[] $userMeetings
 */
class Meeting extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    const STATUS_CREATE = 1;
    const STATUS_STARTED = 5;
    const STATUS_COMPLETED = 10;
    const STATUS_DELETED = 20;

    const TYPE_BASIC = 0;
    const TYPE_GROUP_MEETING = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'meeting';
    }

    /**
     * @param string $training_uuid
     * @param string $start_at
     * @return static
     */
    public static function create(string $training_uuid, string $start_at): self
    {
        $model = new static();

        $model->training_uuid = $training_uuid;
        $model->meeting_uuid = Uuid::uuid6();
        $model->status = static::STATUS_CREATE;
        $model->start_at = $start_at;
        $model->type = self::TYPE_BASIC;

        $model->recordEvent(new MentorCreateMeeting($model->training));

        return $model;
    }

    /**
     * @param string $start_at
     * @param string $end_at
     * @param string $title
     * @param string $description
     * @return static
     */
    public static function createGroupMeeting(string $start_at, string $end_at, string $title, string $description): self
    {
        $model = new static();

        $model->meeting_uuid = Uuid::uuid6();
        $model->status = static::STATUS_CREATE;
        $model->start_at = $start_at;
        $model->end_at = $end_at;
        $model->title = $title;
        $model->description = $description;
        $model->type = self::TYPE_GROUP_MEETING;

        $model->sendAdminCreateGroupMeetingNotification();

        return $model;
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

    public function fields(): array
    {
        return [
            'meeting_uuid',
            'training_uuid',
            'start_at',
            'end_at',
            'status',
            'title',
            'description',
            'type'
        ];
    }

    public function extraFields(): array
    {
        $coaches = [];
        $employees = [];
        $emails = [];

        foreach ($this->userMeetings as $userMeetings) {
            if (!$userMeetings->user) {
                $emails[] = $userMeetings->email;
            } elseif ($userMeetings->user->role == Rbac::ROLE_COACH) {
                $coaches[] = $userMeetings->user->user_uuid;
            } elseif ($userMeetings->user->role == Rbac::ROLE_EMP) {
                $employees[] = $userMeetings->user->user_uuid;
            }
        }

        return [
            'coaches' => function (self $model) use ($coaches) {
                return $coaches;
            },
            'employees' => function (self $model) use ($employees) {
                return $employees;
            },
            'emails' => function (self $model) use ($emails) {
                return $emails;
            },
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function getTraining(): ActiveQuery
    {
        return $this->hasOne(TrainingSession::class, ['training_uuid' => 'training_uuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserMeetings(): ActiveQuery
    {
        return $this->hasMany(UserMeeting::class, ['meeting_uuid' => 'meeting_uuid'])
            ->andOnCondition(['!=', UserMeeting::tableName() . '.status', UserMeeting::STATUS_DELETED]);
    }

    public function toComplete(): void
    {
        $this->status = static::STATUS_COMPLETED;
    }

    public function toDeleted(): void
    {
        $this->status = static::STATUS_DELETED;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function formatStartDate(): string
    {
        $startAt = $this->getNormalizedTime($this->start_at);

        $arr = explode(' ', $startAt);
        $dateString = $arr[0];
        $dateArr = explode('-', $dateString);
        return "$dateArr[2].$dateArr[1].$dateArr[0]";
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function formatStartTime(): string
    {
        $startAt = $this->getNormalizedTime($this->start_at);

        $arr = explode(' ', $startAt);
        $timeString = $arr[1];
        $timeArr = explode(':', $timeString);
        return "$timeArr[0]:$timeArr[1]";
    }

    /**
     * @return ActiveQuery
     */
    public function prepareNotInvitedUsersQuery(): ActiveQuery
    {
        return UserMeeting::find()->joinWith('user')
            ->andWhere([UserMeeting::tableName() . '.meeting_uuid' => $this->meeting_uuid])
            ->andWhere([UserMeeting::tableName() . '.status' => UserMeeting::STATUS_NOT_INVITED])
            ->andWhere([UserMeeting::tableName() . '.notify_status' => UserMeeting::NOTIFY_STATUS_NOT_SEND]);
    }

    /**
     * @return bool
     */
    public function isTypeBasic(): bool
    {
        return $this->type == self::TYPE_BASIC;
    }

    /**
     * @return bool
     */
    public function isTypeGroupMeeting(): bool
    {
        return $this->type == self::TYPE_GROUP_MEETING;
    }

    /**
     * @return bool
     */
    public function isStatusCreated(): bool
    {
        return $this->status == self::STATUS_CREATE;
    }

    /**
     * @return bool
     */
    public function isStatusStarted(): bool
    {
        return $this->status == self::STATUS_STARTED;
    }

    /**
     * @return bool
     */
    public function isStatusDeleted(): bool
    {
        return $this->status == self::STATUS_DELETED;
    }

    /**
     * @param string $token
     * @return string
     */
    public function prepareConfirmLink(string $token): string
    {
        return \Yii::$app->params['frontHost'] . '/' . \Yii::$app->params['confirmGroupMeetingLink'] . '/' . $this->meeting_uuid . '?token=' . $token;
    }

    /**
     * @param string $token
     * @return string
     */
    public function prepareJoinLink(string $token): string
    {
        return \Yii::$app->params['frontHost'] . '/' . \Yii::$app->params['joinGroupMeetingLink'] . '/' . $this->meeting_uuid . '?token=' . $token;
    }

    /**
     * @param $time
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getNormalizedTime(string $time): string
    {
        return \Yii::$app->formatter->asDatetime($time, 'php:Y-m-d H:i:sP');
    }

    public function getNormalizedTimeObject($dateTime): DateTime
    {
        return date_create_from_format('Y-m-d H:i:sP', $dateTime);
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkStartTimeToJoin()
    {
        $start = $this->getNormalizedTime($this->start_at);

        $now = new DateTime();
        $now->modify('+ 10 minutes');
        $now = Yii::$app->formatter->asDatetime($now->format('U'), 'php:Y-m-d H:i:sP');

        return ($now >= $start);
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkTimeToSend24Notification()
    {
        $dateFrom = date_create_from_format('Y-m-d H:i:sP', $this->start_at);
        $dateFrom->modify('- ' . 24 . ' hours');
        $startAtFrom = Yii::$app->formatter->asDatetime($dateFrom->format('U'), 'php:Y-m-d H:i:sP');

        $dateTo = date_create_from_format('Y-m-d H:i:sP', $this->start_at);
        $dateTo->modify('- ' . (24 * 59 * 60) . ' seconds');
        $startAtTo = Yii::$app->formatter->asDatetime($dateTo->format('U'), 'php:Y-m-d H:i:sP');

        $now = new DateTime();
        $now = Yii::$app->formatter->asDatetime($now->format('U'), 'php:Y-m-d H:i:sP');

        return ($now >= $startAtFrom && $now <= $startAtTo);
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkTimeToStartSendNotification()
    {
        $start = $this->getNormalizedTime($this->start_at);

        $now = new DateTime();
        $now = Yii::$app->formatter->asDatetime($now->format('U'), 'php:Y-m-d H:i:sP');

        return ($now >= $start);
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function sendAdminCreateGroupMeetingNotification(): void
    {
        $this->recordEvent(new AdminCreateGroupMeeting($this));
    }

    public function sendCancelMeetingNotification(): void
    {
        $this->recordEvent(new CancelMeetingNotification($this));
    }

    /**
     * @param UserMeeting $userMeeting
     * @param string $previousStartDate
     */
    public function sendMoveMeetingNotification(UserMeeting $userMeeting, string $previousStartDate, string $previousStartTime): void
    {
        $this->recordEvent(new MoveMeetingNotification($this, $userMeeting, $previousStartDate, $previousStartTime));
    }

    /**
     * @return array
     */
    public function getParticipantsEmails(): array
    {
        $emails = array_merge($this->getAuthParticipantsEmails(), $this->getNotAuthParticipantsEmails());
        return array_unique($emails);
    }

    public function getAuthParticipantsEmails()
    {
        return $this->getUserMeetings()
            ->andWhere(UserMeeting::tableName() . '.email is null')
            ->joinWith('user')
            ->andWhere(['!=', User::tableName() . '.user_uuid', $this->created_by])
            ->select(User::tableName() . '.email')
            ->column();
    }

    public function getNotAuthParticipantsEmails()
    {
        return $this->getUserMeetings()
            ->andWhere(UserMeeting::tableName() . '.email is not null')
            ->select(UserMeeting::tableName() . '.email')
            ->column();
    }

    /**
     * @param string $newStartAt
     * @param string $newEndAt
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkMeetingTimeIsChanged(string $newStartAt, string $newEndAt): bool
    {
        $previousStartAt = $this->getNormalizedTime($this->start_at);
        $newStartAt = $this->getNormalizedTime($newStartAt);
        if ($previousStartAt != $newStartAt) {
            return true;
        }

        $previousDateString = $this->getDateString($this->end_at);
        $newDateString = $this->getDateString($newEndAt);
        if ($previousDateString != $newDateString) {
            return true;
        }

        return false;
    }

    /**
     * @param string $date
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function getDateString(string $date): string
    {
        $date = $this->getNormalizedTime($date);
        $arr = explode(' ', $date);
        return $arr[0];
    }
}