<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int|null $send_at
 * @property int|null $group_id
 * @property string|null $text
 * @property int|null $created_by
 * @property int|null $created_at
 *
 * @property NotificationUser[] $notificationUsers
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'send_at'], 'required'],
            [['send_at', 'created_by', 'created_at'], 'default', 'value' => null],
            [['created_by'], 'integer'],
            [['send_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['text'], 'string'],

        ];
    }

}
