<?php

namespace common\models;

use common\models\queries\ClientProgramQuery;
use common\models\traits\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "client_program".
 *
 * @property string $client_uuid
 * @property string $program_uuid
 * @property int|null $status
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property Client $client
 * @property Program $program
 */
class ClientProgram extends ActiveRecord
{
    use EventTrait;

    public const STATUS_DRAFT = 0;
    public const STATUS_DELETED = 1;
    public const STATUS_APPROVED = 10;
    public const STATUS_DECLINE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%client_program}}';
    }

    /**
     * {@inheritdoc}
     * @return ClientProgramQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientProgramQuery(get_called_class());
    }

    public function behaviors()
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
            'client_uuid' => 'Client Uuid',
            'program_uuid' => 'Program Uuid',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public static function create($client_uuid, $program_uuid): self
    {
        $assignment = new static();
        $assignment->program_uuid = $program_uuid;
        $assignment->client_uuid = $client_uuid;
        $assignment->status = self::STATUS_APPROVED;
        return $assignment;
    }

    public function isForProgram($program_uuid): bool
    {
        return $this->program_uuid === $program_uuid;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isForClient($client_uuid): bool
    {
        return $this->client_uuid === $client_uuid;
    }

    public function approve(): void
    {
        $this->status = static::STATUS_APPROVED;
    }

    public function decline($comment): void
    {
        $this->status = static::STATUS_DECLINE;
    }

    public function getProgram() : ActiveQuery
    {
        return $this->hasOne(Program::class,['program_uuid' => 'program_uuid']);
    }

    public function getClient() : ActiveQuery
    {
        return $this->hasOne(Client::class,['client_uuid' => 'client_uuid']);
    }

    public function fields(): array
    {
        return [
            'client_uuid',
            'program_uuid',
            'status'
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
