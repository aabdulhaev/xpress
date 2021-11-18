<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use yii\base\Model;

trait HelperTrait
{

    public function validateBody(Model $model): bool
    {
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        return $model->validate();
    }

    public function validateQuery(Model $model): bool
    {
        $model->load(\Yii::$app->getRequest()->getQueryParams(), '');
        return $model->validate();
    }
}
