<?php

namespace common\models;

use common\models\queries\ClientTariffQuery;
use Ramsey\Uuid\Uuid;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "client_tariff".
 *
 * @property string $client_tariff_uuid
 * @property string $client_uuid
 * @property string $tariff_uuid
 * @property int $expire_at
 * @property int $status
 * @property string $constraint_used
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 */
class ClientTariff extends ActiveRecord
{
    public const STATUS_NEW = 0;
    public const STATUS_DELETED = 1;
    public const STATUS_ACTIVE = 10;
    public const STATUS_CANCEL = 20;
    public const STATUS_EXPIRED = 30;


    public const CONSTRAIN_DEFAULT = ['mentor' => 0, 'coach' => 0, 'employee' => 0];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_tariff';
    }

    /**
     * {@inheritdoc}
     * @return ClientTariffQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientTariffQuery(get_called_class());
    }

    public function behaviors() : array
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class,
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
    public function attributeLabels() : array
    {
        return [
            'client_tariff_uuid' => 'Client Tariff Uuid',
            'client_uuid' => 'Client Uuid',
            'tariff_uuid' => 'Tariff Uuid',
            'expire_at' => 'Expire At',
            'status' => 'Status',
            'constraint_used' => 'Constraint Used',
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
            'client_tariff_uuid',
            'expire_at' => static function(self $model){
                return \Yii::$app->formatter->asDatetime($model->expire_at);
            },
            'constraint_used',
            'status',
            'name' => static function(self $model){
                return \Yii::$app->formatter->asDatetime($model->expire_at);
            },
        ];
    }

    public function extraFields() : array
    {
        return [
            'tariff',
            'client'
        ];
    }

    public static function create($tariff_uuid, $expire_at): self
    {
        $assignment = new static();
        $assignment->tariff_uuid = $tariff_uuid;
        $assignment->client_tariff_uuid = Uuid::uuid6();
        $assignment->status = self::STATUS_ACTIVE;
        $assignment->expire_at = $expire_at;
        $assignment->constraint_used = static::CONSTRAIN_DEFAULT;
        return $assignment;
    }

    public function isForTariff($id): bool
    {
        return $this->tariff_uuid === $id;
    }

    public function getTariff() : ActiveQuery
    {
        return $this->hasOne(TariffPlan::class,['tariff_uuid' => 'tariff_uuid']);
    }

    public function getClient() : ActiveQuery
    {
        return $this->hasOne(Client::class,['client_uuid' => 'client_uuid']);
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCancel(): bool
    {
        return $this->status === self::STATUS_CANCEL;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
