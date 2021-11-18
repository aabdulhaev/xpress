<?php

namespace common\models;

use common\models\events\Stats;
use common\models\queries\SessionRatingQuery;
use common\models\traits\AggregateRoot;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "session_rating".
 *
 * @property string $user_uuid
 * @property string $training_uuid
 * @property string $comment
 * @property int $rate
 * @property string $addon
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property Subject[]|null $subjects
 * @property User $author
 * @property User $rated
 * @property UserStat $userStat
 * @property TrainingSession $training
 * @property bool $is_calculated
 * @property int $status
 */
class SessionRating extends ActiveRecord implements AggregateRoot
{
    use \common\models\traits\EventTrait;

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session_rating}}';
    }

    /**
     * {@inheritdoc}
     * @return SessionRatingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SessionRatingQuery(get_called_class());
    }

    public function behaviors() : array
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uuid' => 'User Uuid',
            'training_uuid' => 'Session Uuid',
            'comment' => 'Comment',
            'rate' => 'Rate',
            'addon' => 'Addon',
            'subjects' => 'Subjects',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public function fields() : array
    {
        return [
            'rate',
            'comment',
            'subjects',
            'author_uuid' => static function(self $model){
                return $model->created_by;
            },
            'rated_uuid' => static function(self $model){
                return $model->user_uuid;
            },
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function isForUser($user_uuid): bool
    {
        return $this->user_uuid === $user_uuid;
    }

    public static function create($user_uuid, $rate, $comment, $subjects) : self
    {
        $model = new static();
        $model->user_uuid = $user_uuid;
        $model->rate = $rate;
        $model->comment = $comment;
        $model->subjects = $subjects;
        $model->recordEvent(new Stats($model));
        return $model;
    }

    public function getAuthor() : ActiveQuery
    {
        return $this->hasOne(User::class,['user_uuid' => 'created_by']);
    }

    public function getRated() : ActiveQuery
    {
        return $this->hasOne(User::class,['user_uuid' => 'user_uuid']);
    }

    public function getUserStat() : ActiveQuery
    {
        return $this->hasOne(UserStat::class,['user_uuid' => 'user_uuid']);
    }

    public function getTraining() : ActiveQuery
    {
        return $this->hasOne(TrainingSession::class,['training_uuid' => 'training_uuid']);
    }

    public function toCalculate():void
    {
        $this->is_calculated = true;
    }


}
