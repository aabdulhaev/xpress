<?php

namespace common\components\helpers;


use common\models\EmployeeMentor;
use common\models\User;

class UserCoachAccessHelper
{
    /**
     * @param User $authUser
     * @param User $user
     * @return bool
     */
    public static function checkAccess(User $authUser, User $user): bool
    {
        // Коуч:

        // нет доступа к профилям других Коучей и Менторов
        if ($user->isUserRoleCoach() || $user->isUserRoleMentor()) {
            return false;
        }

        // доступ только к профилям Сотрудников, с которыми у него созданы пары/группы
        if ($user->isUserRoleEmployee()) {
            /** @var EmployeeMentor $employeeMentor */
            $employeeMentor = $authUser->getEmployees()->andWhere(['user_uuid' => $user->user_uuid])->one();
            return !empty($employeeMentor);
        }

        return false;
    }
}
