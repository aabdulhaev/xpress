<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\UserTraining;

class UserTrainingRepository
{

    public function get($user_uuid, $training_uuid): UserTraining
    {
        return $this->getBy(['training_uuid' => $training_uuid, 'user_uuid' => $user_uuid]);
    }

    public function getByTraining($training_uuid): UserTraining
    {
        return $this->getBy(['training_uuid' => $training_uuid]);
    }

    public function getOther($user_uuid, $training_uuid): UserTraining
    {
        return $this->getBy(['AND', ['training_uuid' => $training_uuid], ['!=', 'user_uuid', $user_uuid]]);
    }

    public function save(UserTraining $model): void
    {
        if (!$model->save()) {
            throw new \RuntimeException('Ошибка сохранения тренинга');
        }
    }

    private function getBy(array $condition): UserTraining
    {
        if (!$training = UserTraining::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Тренинг не найден');
        }
        return $training;
    }
}
