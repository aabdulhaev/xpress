<?php

namespace common\models;

use common\models\queries\MaterialTagQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "material_tags".
 *
 * @property string $material_uuid [uuid]
 * @property string $tag_uuid [uuid]
 * @property int $status [smallint]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 * @property string $blocked_at [integer]
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $blocked_by [uuid]
 *
 * @property Material $material
 * @property Tag $tag
 */
class MaterialTag extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%material_tags}}';
    }

    /**
     * {@inheritdoc}
     * @return MaterialTagQuery the active query used by this AR class.
     */
    public static function find(): MaterialTagQuery
    {
        return new MaterialTagQuery(static::class);
    }

    public function behaviors(): array
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
            ],
        ];
    }

    public static function create($material_uuid, $tag_uuid): self
    {
        $assignment = new static();
        $assignment->material_uuid = $material_uuid;
        $assignment->tag_uuid = $tag_uuid;
        $assignment->status = self::STATUS_ACTIVE;
        return $assignment;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getMaterial(): ActiveQuery
    {
        return $this->hasOne(Material::class, ['material_uuid' => 'material_uuid']);
    }

    public function getTag(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['tag_uuid' => 'tag_uuid']);
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
