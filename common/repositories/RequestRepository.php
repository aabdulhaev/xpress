<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\Request;


class RequestRepository{

    public function find($id): ?Request
    {
        return Request::find()
            ->andWhere(['request_uuid' => $id])
            ->one();
    }

    public function get($id): Request
    {
        if (!$model = Request::findOne($id)) {
            throw new NotFoundException("Запрос {$id} не найден");
        }
        return $model;
    }

    public function save(Request $model): void
    {
        if(!$model->save()){
            throw new \RuntimeException('Ошибка сохранения запроса на регистрацию.');
        }
    }
}
