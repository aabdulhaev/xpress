<?php

namespace console\models;

use Yii;
use yii\behaviors\{BlameableBehavior, TimestampBehavior};
use yii\db\{ActiveQuery, ActiveRecord};


/**
 * This is the model class for table "lesson".
 *
 * @property int $id
 * @property int|null $created_by
 * @property int|null $group_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $record_link
 * @property int|null $number
 * @property bool|null $is_webinar_notification_sent
 * @property bool|null $is_notification_sent_to_user
 * @property int|null $status
 * @property int|null $teacher_id
 * @property string|null $title
 * @property int|null $updated_by
 * @property int|null $homework_max_score
 * @property string|null $webinar_link
 * @property string|null $description
 * @property int|null $webinar_date
 * @property boolean $has_test
 * @property boolean $has_homework
 */
class Lesson extends ActiveRecord
{
    public const STATUS_ARCHIVE = 0;
    public const STATUS_DRAFT = 1;
    public const STATUS_PUBLISHED = 2;
    public const STATUS_HIDDEN = 3;

    public const UNAVAILABLE = 0;
    public const PENDING = 1;
    public const ON_CHECK = 2;
    public const COMPLETED = 3;

    public $progress;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%lesson}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'teacher_id', 'title', 'number'], 'required'],
            [['group_id', 'number', 'status', 'teacher_id', 'webinar_date', 'homework_max_score'], 'integer'],
            [['is_webinar_notification_sent', 'is_notification_sent_to_user'], 'boolean'],
            [['description'], 'string'],
            [['record_link', 'title', 'webinar_link'], 'string', 'max' => 255],
            [['is_webinar_notification_sent'], 'default', 'value' => false],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [
                'status',
                'in',
                'range' => [self::STATUS_ARCHIVE, self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_HIDDEN]
            ],
            [['progress'], 'safe'],
            [['has_test'], 'boolean'],
            [['has_homework'], 'boolean'],
            [['number'], 'number', 'min' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                           => 'ID',
            'created_by'                   => 'Created By',
            'group_id'                     => 'Group ID',
            'created_at'                   => 'Created At',
            'updated_at'                   => 'Updated At',
            'record_link'                  => 'Record Link',
            'number'                       => 'Number',
            'is_webinar_notification_sent' => 'Is Webinar Notification Sent',
            'status'                       => 'Status',
            'teacher_id'                   => 'Teacher ID',
            'title'                        => 'Title',
            'updated_by'                   => 'Updated By',
            'webinar_link'                 => 'Webinar Link',
            'description'                  => 'Description',
            'webinar_date'                 => 'Webinar Date',
            'homework_max_score'           => 'Homework Max Score',
        ];
    }



    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function fields()
    {
        return [
            'id',
            'group_id',
            'teacher_id',
            'title',
            'description',
            'number',
            'status',
            'record_link',
            'webinar_link',
            'webinar_date',
            'homework_max_score',
            'progress',
        ];
    }

}

