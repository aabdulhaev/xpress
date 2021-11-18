<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\Client;


class ClientRepository{

    public function find($id): ?Client
    {
        return Client::find()
            ->andWhere(['client_uuid' => $id])
            ->one();
    }

    public function get($id): Client
    {
        $model = Client::findOne($id);
        if (!$model) {
            throw new NotFoundException("Клиент {$id} не найден");
        }
        return $model;
    }

    public function save(Client $model): void
    {
        if (!$model->save()) {
            \Yii::error($model->errors);
            throw new \RuntimeException('Ошибка сохранения.');
        }
    }

}
