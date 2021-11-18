<?php

namespace common\models;

use common\models\queries\TariffPlanQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "tariff_plan".
 *
 * @property string $tariff_uuid
 * @property string $name
 * @property string $description
 * @property float $cost
 * @property array $constraints
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $status
 */
class TariffPlan extends ActiveRecord
{
    public const SEED_TARIFF_UUID = '1eb137eb-e3d0-6850-300b-6ac5f0f3224f';

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff_plan';
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
     * @return TariffPlanQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TariffPlanQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_uuid', 'name', 'description', 'cost', 'constraints', 'created_at', 'created_by'], 'required'],
            [['tariff_uuid', 'created_by', 'updated_by', 'blocked_by'], 'string'],
            [['cost'], 'number'],
            [['constraints'], 'safe'],
            [['created_at', 'updated_at', 'blocked_at'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'blocked_at'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 20148],
            [['tariff_uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tariff_uuid' => 'Tariff Uuid',
            'name' => 'Name',
            'description' => 'Description',
            'cost' => 'Cost',
            'constraints' => 'Constraints',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public function getExpireTime() : int
    {
        return $this->constraints['expire'] ?? 0;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
