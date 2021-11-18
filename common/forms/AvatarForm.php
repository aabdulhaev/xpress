<?php

namespace common\forms;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * @OA\Schema()
 */
class AvatarForm extends Model
{
    /**
     * @OA\Property(type="file")
     * @var string
     */
    public $avatar;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*['avatar', 'image', 'skipOnEmpty' => false, 'extensions' => ['png, jpg, jpeg'],
                'wrongExtension' => "'{attribute}' должен быть расширением png, jpg, jpeg."],*/
            ['avatar', 'image', 'maxSize' => 1024 * 1024 * 10, 'tooBig' => "'{attribute}' не должен быть больше 10MB"],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'avatar' => 'Аватар',
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->avatar = UploadedFile::getInstanceByName('avatar');
            return true;
        }
        return false;
    }
}
