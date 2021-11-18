<?php

namespace common\models;

use common\models\queries\UserSectionQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_sections".
 *
 * @property string $user_uuid [uuid]
 * @property string $section_uuid [uuid]
 * @property int $status [smallint]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 * @property string $blocked_at [integer]
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $blocked_by [uuid]
 *
 * @property Section $section
 * @property User $user
 */
class UserSection extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user_sections}}';
    }

    /**
     * {@inheritdoc}
     * @return UserSectionQuery the active query used by this AR class.
     */
    public static function find(): UserSectionQuery
    {
        return new UserSectionQuery(get_called_class());
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

    public static function create($section_uuid): self
    {
        $assignment = new static();
        $assignment->section_uuid = $section_uuid;
        $assignment->status = self::STATUS_ACTIVE;
        return $assignment;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_uuid' => 'user_uuid']);
    }

    public function getSection(): ActiveQuery
    {
        return $this->hasOne(Section::class, ['section_uuid' => 'section_uuid']);
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
