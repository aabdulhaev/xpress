<?php

namespace common\models;

use common\models\queries\MaterialUserQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "material_user_actions".
 *
 * @property string $material_uuid [uuid]
 * @property string $user_uuid [uuid]
 * @property int $accessed [smallint]
 * @property int $elected [smallint]
 * @property int $learned [smallint]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 * @property string $blocked_at [integer]
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $blocked_by [uuid]
 *
 * @property bool $isLearned
 * @property bool $isElected
 * @property bool $isAccessed
 * @property int $status [smallint]
 */
class MaterialUser extends ActiveRecord
{
    public const NOT_LEARNED = 0;
    public const LEARNED = 1;

    public const NOT_ELECTED = 0;
    public const ELECTED = 1;

    public const NOT_ACCESSED = 0;
    public const ACCESSED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%material_users}}';
    }

    /**
     * {@inheritdoc}
     * @return MaterialUserQuery the active query used by this AR class.
     */
    public static function find(): MaterialUserQuery
    {
        return new MaterialUserQuery(static::class);
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
                    'accessed' => self::NOT_ACCESSED
                ]
            ],
        ];
    }

    public static function create($material_uuid, $user_uuid, $accessed = self::NOT_ACCESSED, $elected = self::NOT_ELECTED, $learned = self::NOT_LEARNED): self
    {
        $assignment = new static();
        $assignment->material_uuid = $material_uuid;
        $assignment->user_uuid = $user_uuid;
        $assignment->accessed = $accessed;
        $assignment->elected = $elected;
        $assignment->learned = $learned;
        return $assignment;
    }

    public function getIsLearned(): bool
    {
        return $this->learned == self::LEARNED;
    }

    public function getIsElected(): bool
    {
        return $this->elected == self::ELECTED;
    }

    public function getIsAccessed(): bool
    {
        return $this->accessed == self::ACCESSED;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
