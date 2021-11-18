<?php

namespace console\models;

/**
 * This is the model class for table "notification_user".
 *
 * @property int|null $notification_id
 * @property int|null $user_id
 * @property int|null $status
 *
 */
class NotificationUser extends \yii\db\ActiveRecord
{
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_DELAYED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notification_user}}';
    }

    /**
     * @return string[]
     */
    public static function primaryKey()
    {
        return ['notification_id', 'user_id'];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'user_id', 'status'], 'default', 'value' => null],
            [['notification_id', 'user_id', 'status'], 'integer'],
            [
                'status',
                'in',
                'range' => [self::STATUS_DELAYED, self::STATUS_UNREAD, self::STATUS_READ]
            ],
        ];
    }


}
