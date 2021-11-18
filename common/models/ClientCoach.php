<?php

namespace common\models;

use common\models\queries\ClientCoachQuery;
use common\models\traits\AggregateRoot;
use common\models\traits\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "client_coach".
 *
 * @property string $client_uuid
 * @property string $coach_uuid
 * @property int|null $status
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property string|null $comment
 * @property Client $client
 * @property User $coach
 */
class ClientCoach extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public const STATUS_DRAFT = 0;
    public const STATUS_APPROVED = 10;
    public const STATUS_DECLINE = 20;


    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return '{{%client_coach}}';
    }

    /**
     * {@inheritdoc}
     * @return ClientCoachQuery the active query used by this AR class.
     */
    public static function find() : ClientCoachQuery
    {
        return new ClientCoachQuery(static::class);
    }

    public static function create($client_uuid, $coach_uuid): self
    {
        $assignment = new static();
        $assignment->client_uuid = $client_uuid;
        $assignment->coach_uuid = $coach_uuid;
        $assignment->approve();
        return $assignment;
    }

    public function behaviors() : array
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DRAFT
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'client_uuid' => 'Employee Uuid',
            'coach_uuid' => 'Mentor Uuid',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
            'comment' => 'Comment'
        ];
    }

    public function fields()
    {
        return [
            'client_uuid',
            'coach_uuid',
            'status',
            'comment'
        ];
    }

    public function isForClient($id): bool
    {
        return $this->client_uuid === $id;
    }

    public function isForCoach($id): bool
    {
        return $this->coach_uuid === $id;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDecline(): bool
    {
        return $this->status === self::STATUS_DECLINE;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function approve(): void
    {
        $this->status = static::STATUS_APPROVED;
    }

    public function decline($comment): void
    {
        $this->status = static::STATUS_DECLINE;
        $this->comment = $comment;
    }

    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['client_uuid' => 'client_uuid']);
    }

    public function getCoach(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_uuid' => 'coach_uuid']);
    }
}
