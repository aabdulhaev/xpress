<?php


namespace common\models;

use common\models\queries\CompetenceQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "competence".
 *
 * @property string $competence_uuid
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
class Competence extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%competence}}';
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
     * @return CompetenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CompetenceQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['competence_uuid', 'title', 'created_at', 'created_by'], 'required'],
            [['competence_uuid', 'created_by', 'updated_by', 'blocked_by'], 'string'],
            [['created_at', 'updated_at', 'blocked_at'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'blocked_at'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 2048],
            [['img_name'], 'string', 'max' => 1048],
            [['competence_uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'competence_uuid' => 'Competence Uuid',
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
            'competence_uuid',
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
            /** @var Competence $competence */
            $competence = Competence::findOne(['title' => $this->title]);
            return empty($competence);
        } else {
            return false;
        }
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
