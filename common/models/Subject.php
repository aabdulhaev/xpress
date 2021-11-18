<?php

namespace common\models;

use common\models\queries\SubjectQuery;
use common\models\traits\EventTrait;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "subject".
 *
 * @property string $subject_uuid
 * @property string $title
 * @property string|null $description
 * @property string|null $img_name
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $status
 */
class Subject extends ActiveRecord
{
    use EventTrait;

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subject}}';
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
     * @return SubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubjectQuery(get_called_class());
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
            [['subject_uuid', 'title', 'created_at', 'created_by'], 'required'],
            [['subject_uuid', 'created_by', 'updated_by', 'blocked_by'], 'string'],
            [['created_at', 'updated_at', 'blocked_at'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'blocked_at'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 2048],
            [['img_name'], 'string', 'max' => 1048],
            [['subject_uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subject_uuid' => 'Subject Uuid',
            'title' => 'Title',
            'description' => 'Description',
            'img_name' => 'Image file name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public function fields()
    {
        return [
            'subject_uuid',
            'title',
            'description',
            'img_name'
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            /** @var Subject $subject */
            $subject = Subject::findOne(['title' => $this->title]);
            return empty($subject);
        } else {
            return false;
        }
    }
}
