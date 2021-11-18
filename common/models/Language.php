<?php

namespace common\models;

use common\models\queries\LanguageQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * @OA\Schema()
 *
 * @OA\Property(property="language_uuid", type="string")
 * @OA\Property(property="title", type="string")
 * @OA\Property(property="description", type="string")
 * @OA\Property(property="status", type="int")
 *
 * This is the model class for table "languages".
 *
 * @property string $language_uuid [uuid]
 * @property string $title [varchar(32)]
 * @property string $description [varchar(2048)]
 * @property int $status [smallint]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 * @property string $blocked_at [integer]
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $blocked_by [uuid]
 * @property bool $isDeleted
 */
class Language extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%languages}}';
    }

    /**
     * {@inheritdoc}
     * @return LanguageQuery the active query used by this AR class.
     */
    public static function find(): LanguageQuery
    {
        return new LanguageQuery(get_called_class());
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
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        return [
            'language_uuid',
            'title',
            'description',
            'status',
        ];
    }

    public function getIsDeleted(): bool
    {
        return $this->status == self::STATUS_DELETED;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
