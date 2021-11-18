<?php

namespace common\models;

use common\models\queries\UserSubjectQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_subject".
 *
 * @property string $user_uuid
 * @property string $subject_uuid
 * @property int $created_at
 * @property int|null $updated_at
 * @property int $status
 */
class UserSubject extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_subject}}';
    }

    /**
     * {@inheritdoc}
     * @return UserSubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserSubjectQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uuid' => 'User Uuid',
            'subject_uuid' => 'Subject Uuid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    public function behaviors()
    {
        return [
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

    public static function create($subject_uuid): self
    {
        $assignment = new static();
        $assignment->subject_uuid = $subject_uuid;
        return $assignment;
    }
}
