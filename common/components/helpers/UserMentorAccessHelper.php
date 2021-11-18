<?php

namespace common\components\helpers;


use common\models\EmployeeMentor;
use common\models\User;

class UserMentorAccessHelper
{
    /**
     * @param User $authUser
     * @param User $user
     * @return bool
     */
    public static function checkAccess(User $authUser, User $user): bool
    {
        // Ментор:

        // нет доступа к профилям других Менторов и Коучей
        if ($user->isUserRoleMentor() || $user->isUserRoleCoach()) {
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
