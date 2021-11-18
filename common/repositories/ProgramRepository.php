<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\Program;


class ProgramRepository{

    public function find($id): ?Program
    {
        return Program::find()
            ->andWhere(['program_uuid' => $id])
            ->one();
    }

    public function get($id): Program
    {
        if (!$model = Program::findOne($id)) {
            throw new NotFoundException("Программа {$id} не найдена");
        }
        return $model;
    }

}
