<?php

namespace common\models;

use common\models\queries\NotificationQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "notification".
 *
 * @property string $notification_uuid
 * @property int $type
 * @property string $body
 * @property string $title
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $status
 */
class Notification extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
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
     * @return NotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationQuery(get_called_class());
    }

    public function delete()
    {
        return $this->softDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_uuid', 'type', 'body', 'title', 'created_at', 'created_by'], 'required'],
            [['notification_uuid', 'body', 'created_by', 'updated_by', 'blocked_by'], 'string'],
            [['type', 'created_at', 'updated_at', 'blocked_at'], 'default', 'value' => null],
            [['type', 'created_at', 'updated_at', 'blocked_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['notification_uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notification_uuid' => 'Notification Uuid',
            'type' => 'Type',
            'body' => 'Body',
            'title' => 'Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }
}
