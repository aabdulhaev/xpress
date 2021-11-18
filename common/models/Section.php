<?php

namespace common\models;

use common\models\queries\SectionQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "sections".
 *
 * @property string $section_uuid [uuid]
 * @property string $title [varchar(32)]
 * @property string $description [varchar(2048)]
 * @property int $status [smallint]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 * @property string $blocked_at [integer]
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $blocked_by [uuid]
 */
class Section extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;

    public const SECTION_LIBRARY_UUID = '1ec0b07f-e3f9-6aa4-9052-1860247f37f2';
    public const SECTION_WEBINAR_UUID = '1ec0b07f-e4c6-6b3a-8ad5-1860247f37f2';
    public const SECTION_POLL_UUID = '1ec0b07f-e4c7-6d0a-91c6-1860247f37f2';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%sections}}';
    }

    /**
     * {@inheritdoc}
     * @return SectionQuery the active query used by this AR class.
     */
    public static function find(): SectionQuery
    {
        return new SectionQuery(get_called_class());
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
            'section_uuid',
            'title',
            'description',
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
