<?php

namespace common\repositories;

use common\forms\DirectoryForm;
use common\models\Language;
use common\models\Tag;
use common\models\Theme;
use Ramsey\Uuid\Uuid;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * @property ActiveRecord|Tag|Theme|Language $modelClass
 */
class DirectoryRepository
{
    const DIRECTORY_THEME = 'theme';
    const DIRECTORY_TAG = 'tag';
    const DIRECTORY_LANGUAGE = 'language';

    public $primaryKey;
    public $modelClass;
    public $directories = [
        'theme' => Theme::class,
        'tag' => Tag::class,
        'language' => Language::class,
    ];

    public function checkDirectory($directory): void
    {
        if (!array_key_exists($directory, $this->directories)) {
            throw new NotFoundHttpException("Справочник не найден");
        }

        $this->modelClass = $this->directories[$directory];
        $this->primaryKey = $this->modelClass::primaryKey();
    }

    public function get($id)
    {
        $model = $this->modelClass::findOne($id);
        if (!$model || $model->isDeleted) {
            throw new NotFoundHttpException('Элемент справочника не найден');
        }

        return $model;
    }

    public function save($model): void
    {
        /* @var $model ActiveRecord */
        if (!$model->save()) {
            throw new BadRequestHttpException('Ошибка сохранения элемента справочника');
        }
    }

    public function remove($model): void
    {
        /* @var $model ActiveRecord */
        if (!$model->delete()) {
            throw new BadRequestHttpException('Ошибка удаления элемента справочника');
        }
    }

    public function create(DirectoryForm $form)
    {
        $model = new $this->modelClass;

        $model->setAttributes($form->toArray(), false);
        $model->{$this->primaryKey[0]} = Uuid::uuid6();
        $model->status = $this->modelClass::STATUS_ACTIVE;

        $this->save($model);

        return $model;
    }

    public function update($model, DirectoryForm $form): void
    {
        $model->setAttributes($form->toArray(), false);
        $this->save($model);
    }
}
