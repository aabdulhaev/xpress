<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\TariffPlan;


class TariffRepository{

    public function find($id): ?TariffPlan
    {
        return TariffPlan::find()
            ->andWhere(['tariff_uuid' => $id])
            ->one();
    }

    public function get($id): TariffPlan
    {
        if (!$model = TariffPlan::findOne($id)) {
            throw new NotFoundException("Тариф {$id} не найден");
        }
        return $model;
    }

    public function save(TariffPlan $model): void
    {
        if(!$model->save()){
            throw new \RuntimeException('Ошибка сохранения.');
        }
    }

}
