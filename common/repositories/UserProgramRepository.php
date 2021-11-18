<?php

namespace common\repositories;

use common\models\UserProgram;

class UserProgramRepository
{
    public function save(UserProgram $model): void
    {
        if(!$model->save()){
            throw new \RuntimeException('Ошибка сохранения программы пользователя.');
        }
    }
}
