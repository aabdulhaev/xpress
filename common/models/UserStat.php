<?php

namespace common\models;

use common\access\Rbac;
use common\models\queries\UserStatQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_stat".
 *
 * @property string $user_uuid
 * @property int|null $mentor_rating
 * @property int|null $coach_rating
 *
 * @property int|null $mentor_session_completed
 * @property int|null $mentor_session_planned
 * @property int|null $mentor_session_canceled
 * @property int|null $coach_session_completed
 * @property int|null $coach_session_planned
 * @property int|null $coach_session_canceled
 *
 * @property int $status
 *
 * @property User $owner
 */
class UserStat extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_stat';
    }

    public function behaviors(): array
    {
        return [
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
     * @return UserStatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserStatQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uuid' => 'User Uuid',
            'mentor_rating' => 'Rating',
            'coach_rating' => 'Rating',
            'mentor_session_completed' => 'Mentor Session Completed',
            'mentor_session_planned' => 'Mentor Session Planned',
            'mentor_session_canceled' => 'Mentor Session Canceled',
            'coach_session_completed' => 'Coach Session Completed',
            'coach_session_planned' => 'Coach Session Planned',
            'coach_session_canceled' => 'Coach Session Canceled',
        ];
    }

    public static function create(string $user_uuid) : self
    {
        $model = new static();

        $model->mentor_session_completed = 0;
        $model->mentor_session_planned = 0;
        $model->mentor_session_canceled = 0;

        $model->coach_session_completed = 0;
        $model->coach_session_planned = 0;
        $model->coach_session_canceled = 0;

        $model->user_uuid = $user_uuid;

        $model->mentor_rating = 0;
        $model->coach_rating = 0;
        return $model;
    }

    public function fields()
    {
        return [
            'mentor' => static function(self $stat){
                return [
                    'completed' => $stat->mentor_session_completed,
                    'canceled' => $stat->mentor_session_canceled,
                    'planned' => $stat->mentor_session_planned,
                    'avg_rating' => $stat->mentor_rating
                ];
            },
            'coach' => static function(self $stat){
                return [
                    'completed' => $stat->coach_session_completed,
                    'canceled' => $stat->coach_session_canceled,
                    'planned' => $stat->coach_session_planned,
                    'avg_rating' => $stat->coach_rating
                ];
            }
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function getOwner()
    {
        return $this->hasOne(User::class,['user_uuid' => 'user_uuid']);
    }
}
