<?php

namespace common\components\helpers;


use common\models\EmployeeMentor;
use common\models\User;

class UserEmployeeAccessHelper
{
    /**
     * @param User $authUser
     * @param User $user
     * @return bool
     */
    public static function checkAccess(User $authUser, User $user): bool
    {
        //Сотрудник:

        // нет доступа к профилям других Сотрудников
        if ($user->isUserRoleEmployee()) {
            return false;
        }
        // доступ только к профилям Коучей/Менторов, с которыми у него созданы пары/группы
        if ($user->isUserRoleCoach() || $user->isUserRoleMentor()) {
            /** @var EmployeeMentor $employeeMentor */
            $employeeMentor = $user->getEmployees()->andWhere(['user_uuid' => $authUser->user_uuid])->one();
            return !empty($employeeMentor);
        }

        return false;
    }
}
