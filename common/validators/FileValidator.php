<?php

namespace common\validators;

use Yii;
use yii\helpers\FileHelper;
use yii\validators\FileValidator as Origin;
use yii\web\UploadedFile;

class FileValidator extends Origin
{
    public $fileMap;
    protected $extSize;

    public function init()
    {
        parent::init();
        if (is_array($this->fileMap)) {
            foreach ($this->fileMap as $conf) {
                if (is_array($conf)) {
                    if (isset($conf['extensions'])){
                        if (!is_array($conf['extensions'])) {
                            $extensions = preg_split('/[\s,]+/', strtolower($conf['extensions']), -1, PREG_SPLIT_NO_EMPTY);
                        } else {
                            $extensions = array_map('strtolower', $conf['extensions']);
                        }
                        foreach ($extensions as $item) {
                            $this->extSize[$item] = isset($conf['maxSize']) ? (int) $conf['maxSize'] : null;
                        }
                        $this->extensions = array_merge($this->extensions, $extensions);
                    }
                }
            }
        }
        if ($this->tooBig == Yii::t('yii', 'The file "{file}" is too big. Its size cannot exceed {formattedLimit}.')) {
            $this->tooBig = Yii::t(
                'yii',
                'Файл "{file}" слишком большой. Размер файла с расширением {extension} не должен превышать {formattedLimit}.'
            );
        }
        if ($this->wrongExtension === Yii::t('yii', 'Only files with these extensions are allowed: {extensions}.')) {
            $this->wrongExtension = Yii::t('yii', 'Вы загружаете {file}. Разрешена загрузка файлов только со следующими расширениями: {extensions}.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        if (!$value instanceof UploadedFile || $value->error == UPLOAD_ERR_NO_FILE) {
            return [$this->uploadRequired, []];
        }

        /** @var $value UploadedFile */
        if ($value->extension && isset($this->extSize[$value->extension])) {
            $this->maxSize = $this->extSize[$value->extension];
        }

        switch ($value->error) {
            case UPLOAD_ERR_OK:
                if ($this->maxSize !== null && $value->size > $this->getSizeLimit()) {
                    return [
                        $this->tooBig,
                        [
                            'file' => $value->name,
                            'extension' => $value->extension,
                            'limit' => $this->getSizeLimit(),
                            'formattedLimit' => Yii::$app->formatter->asShortSize($this->getSizeLimit()),
                        ],
                    ];
                } elseif ($this->minSize !== null && $value->size < $this->minSize) {
                    return [
                        $this->tooSmall,
                        [
                            'file' => $value->name,
                            'limit' => $this->minSize,
                            'formattedLimit' => Yii::$app->formatter->asShortSize($this->minSize),
                        ],
                    ];
                } elseif (!empty($this->extensions) && !$this->validateExtension($value)) {
                    return [
                        $this->wrongExtension,
                        ['file' => $value->name, 'extensions' => implode(', ', $this->extensions)]
                    ];
                } elseif (!empty($this->mimeTypes) && !$this->validateMimeType($value)) {
                    return [
                        $this->wrongMimeType,
                        ['file' => $value->name, 'mimeTypes' => implode(', ', $this->mimeTypes)]
                    ];
                }

                return null;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [
                    $this->tooBig,
                    [
                        'file' => $value->name,
                        'extension' => $value->extension,
                        'limit' => $this->getSizeLimit(),
                        'formattedLimit' => Yii::$app->formatter->asShortSize($this->getSizeLimit()),
                    ]
                ];
            case UPLOAD_ERR_PARTIAL:
                Yii::warning('File was only partially uploaded: ' . $value->name, __METHOD__);
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                Yii::warning('Missing the temporary folder to store the uploaded file: ' . $value->name, __METHOD__);
                break;
            case UPLOAD_ERR_CANT_WRITE:
                Yii::warning('Failed to write the uploaded file to disk: ' . $value->name, __METHOD__);
                break;
            case UPLOAD_ERR_EXTENSION:
                Yii::warning('File upload was stopped by some PHP extension: ' . $value->name, __METHOD__);
                break;
            default:
                break;
        }

        return [$this->message, []];
    }

    protected function validateExtension($file)
    {
        $extension = mb_strtolower($file->extension, 'UTF-8');

        if ($this->checkExtensionByMimeType) {
            $mimeType = FileHelper::getMimeType($file->tempName, null, false);
            if ($mimeType === null) {
                return false;
            }

            if ($mimeType == 'application/x-rar'){
                $mimeType = 'application/x-rar-compressed';
            }
            if ($mimeType == 'text/rtf'){
                $mimeType = 'application/rtf';
            }
            if ($mimeType == 'video/x-ms-asf'){
                $mimeType = 'audio/x-ms-wma';
            }
            if ($mimeType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheetapplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            }

            $extensionsByMimeType = FileHelper::getExtensionsByMimeType($mimeType);

            if (!in_array($extension, $extensionsByMimeType, true)) {
                return false;
            }
        }
        return true;
    }

}