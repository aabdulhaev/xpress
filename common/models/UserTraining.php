<?php

declare(strict_types=1);

namespace common\models;

use common\forms\user\UserTrainingCreateForm;
use common\models\queries\UserTrainingQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_training".
 *
 * @property string $user_uuid
 * @property string $training_uuid
 * @property int $status
 * @property string $comment
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $notify_status
 * @property string $google_event_id
 * @property User $user
 * @property TrainingSession $training
 */

class UserTraining extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_NOT_CONFIRM = 5;
    public const STATUS_CONFIRM = 10;
    public const STATUS_CANCEL = 15;
    public const STATUS_NOT_ESTIMATE = 20;
    public const STATUS_ESTIMATE = 25;

    public const NOTIFY_STATUS_NOT_SEND = 0;
    public const NOTIFY_STATUS_48_SEND = 10;
    public const NOTIFY_STATUS_24_SEND = 20;

    public static function statuses(): array
    {
        return [
            self::STATUS_DELETED => 'Удалено',
            self::STATUS_NOT_CONFIRM => 'Не подтвержден',
            self::STATUS_CONFIRM => 'Подтвержден',
            self::STATUS_CANCEL => 'Отменен',
            self::STATUS_NOT_ESTIMATE => 'Не оценен',
            self::STATUS_ESTIMATE => 'Оценен'
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_training';
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

    public function delete()
    {
        return $this->softDelete();
    }

    /**
     * {@inheritdoc}
     * @return UserTrainingQuery the active query used by this AR class.
     */
    public static function find(): UserTrainingQuery
    {
        return new UserTrainingQuery(static::class);
    }

    public function toCancel(): void
    {
        $this->status = static::STATUS_CANCEL;
    }

    public function toNotConfirm(): void
    {
        $this->status = static::STATUS_NOT_CONFIRM;
    }

    public function toConfirm(): void
    {
        $this->status = static::STATUS_CONFIRM;
    }

    public function toEstimate(): void
    {
        $this->status = static::STATUS_ESTIMATE;
    }

    public function toNotEstimate(): void
    {
        $this->status = static::STATUS_NOT_ESTIMATE;
    }

    public function toDeleted(): void
    {
        $this->status = static::STATUS_DELETED;
    }

    public function addComment(string $comment = null): void
    {
        $this->comment = $comment;
    }

    public function isForUser($user_uuid): bool
    {
        return $this->user_uuid === $user_uuid;
    }

    /**
     * @param UserTrainingCreateForm $form
     * @return static
     */
    public static function create(UserTrainingCreateForm $form): self
    {
        $model = new static();
        $model->user_uuid = $form->user_uuid;
        $model->status = $form->status;
        $model->comment = $form->comment;
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


    public function fields(): array
    {
        return [
            'status',
            'comment',
            'user_uuid'
        ];
    }
}
