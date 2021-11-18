<?php

namespace common\models;

use common\models\queries\UserNotificationQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_notification".
 *
 * @property string $user_uuid
 * @property string $notification_uuid
 * @property int $status
 * @property int $channel
 */
class UserNotification extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_notification';
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
     * @return UserNotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserNotificationQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_uuid', 'notification_uuid', 'status', 'channel'], 'required'],
            [['user_uuid', 'notification_uuid'], 'string'],
            [['status', 'channel'], 'default', 'value' => null],
            [['status', 'channel'], 'integer'],
            [['user_uuid', 'notification_uuid'], 'unique', 'targetAttribute' => ['user_uuid', 'notification_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uuid' => 'User Uuid',
            'notification_uuid' => 'Notification Uuid',
            'status' => 'Status',
            'channel' => 'Channel',
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
