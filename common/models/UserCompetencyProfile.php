<?php

namespace common\models;

use common\behaviors\uploadBehavior\ImageUploadBehavior;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "{{%user_competency_profile}}".
 *
 * @property string $program_uuid
 * @property string $user_uuid
 * @property string|null $image
 * @property int|null $created_at
 * @property int $status
 *
 * @property User $owner
 */
class UserCompetencyProfile extends \yii\db\ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_competency_profile}}';
    }


    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false
            ],
            'img' => [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'image',
                'createThumbsOnRequest' => true,
                'filePath' => '@storageRoot/origin/competency_profile/[[attribute_pk_uuid]]/[[pk]].[[extension]]',
                'fileUrl' => '@storage/origin/competency_profile/[[attribute_pk_uuid]]/[[pk]].[[extension]]',
                'thumbPath' => '@storageRoot/cache/competency_profile/[[attribute_pk_uuid]]/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '@storage/cache/competency_profile/[[attribute_pk_uuid]]/[[profile]]_[[pk]].[[extension]]',
                'thumbs' => [
                    'thumb' => ['width' => 320, 'height' => 222],
                ],
            ],
            'createUuid'=>[
                'class'=>AttributeBehavior::class,
                'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT=>'pk_uuid'],
                'value' => function($event){
                    return Uuid::uuid6();
                }
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    public static function primaryKey()
    {
        return ['pk_uuid'];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                'image',
                'image',
                'skipOnEmpty' => false,
                'minWidth'=>378,
                'minHeight'=>222,
                'extensions' => 'png, jpg, jpeg','mimeTypes'=>'image/*'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'program_uuid' => 'Program Uuid',
            'user_uuid' => 'User Uuid',
            'image' => 'Image',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['user_uuid' => 'user_uuid']);
    }

    public function getOrigin()
    {
         return $this->getBehavior('img')->getImageFileUrl('image');
    }

    public function getPreview($profile = 'thumb')
    {
        return $this->getBehavior('img')->getThumbFileUrl('image', $profile);
    }

    public function fields()
    {
        return[
            'pk_uuid','user_uuid',
            'preview',
            'origin',
            'created_at',
            'created_at_format'=>function($model){
                return Yii::$app->formatter->asDatetime($model->created_at,'php:d-m-Y H:i');
            },
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
