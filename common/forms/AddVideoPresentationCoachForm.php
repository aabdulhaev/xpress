<?php

declare(strict_types=1);

namespace common\forms;

use common\validators\VideoValidator;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * @OA\Schema(required={"video"})
 * @property UploadedFile $video
 */
class AddVideoPresentationCoachForm extends Model
{
    /**
     * @OA\Property(type="file")
     * @var string
     */
    public $video;

    public function rules(): array
    {
        return [
            ['video', 'required'],
            [
                'video',
                VideoValidator::class,
                'skipOnEmpty' => false,
                'extensions' => 'mp4, mpeg4, mov, avi, flv, wmv',
                'mimeTypes' => 'video/*',
                'maxDuration' => 30,
                'maxSize' => 1024 * 1024 * 100
            ],
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->video = UploadedFile::getInstanceByName('video');
            return true;
        }

        return false;
    }

    public function attributeLabels(): array
    {
        return [
            'video' => 'Видео-презентация',
        ];
    }
}
