<?php

namespace common\validators;

use JamesHeinrich\GetID3\GetID3;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

class VideoValidator extends FileValidator
{
    /**
     * @var int max duration of video
     */
    public $maxDuration;
    /**
     * @var string the error message used when duration of the video is over [[maxDuration]].
     * You may use the following tokens in the message:
     *
     * - {file}: the uploaded file name
     * - {limit}: the value of [[maxHeight]]
     */
    public $overDurationMessage = 'Продолжительность видео «{file}» больше разрешённых {limit} секунд.';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $result = parent::validateValue($value);

        return empty($result) ? $this->validateDuration($value) : $result;
    }

    /**
     * Validates an image file.
     * @param UploadedFile $media uploaded file passed to check against a set of rules
     * @return array|null the error message and the parameters to be inserted into the error message.
     * Null should be returned if the data is valid.
     */
    public function validateDuration($media)
    {
        $getId3 = new GetId3();
        $info = $getId3->analyze($media->tempName);

        $duration = intval($info['playtime_seconds']);

        if ($duration > $this->maxDuration) {
            return [$this->overDurationMessage, [
                'file' => $media->name,
                'limit' => $this->maxDuration,
            ]];
        }

        return null;
    }
}