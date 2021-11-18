<?php

namespace common\components\helpers;


use common\access\Rbac;
use common\models\ClientCoach;
use common\models\EmployeeMentor;
use common\models\User;

class UserHrRoleAccessHelper
{
    /**
     * @param User $authUser
     * @param User $user
     * @return bool
     */
    public static function checkAccess(User $authUser, User $user): bool
    {
        // HR-админ

        // доступ только к профилям Сотрудников той же компании
        if ($user->isUserRoleEmployee()) {
            return $authUser->client_uuid == $user->client_uuid;
        }

        // доступ только к профилям Коучей, назначенных для той же компании
        if ($user->isUserRoleCoach()) {
            /** @var ClientCoach $clientCoach */
            $clientCoach = $authUser->getClientCoachesAssignments()->andWhere(['client_coach.coach_uuid' => $user->user_uuid])->one();
            return !empty($clientCoach);
        }

        // доступ только к профилям Менторов, с которыми у сотрудников той же компании созданы группы/пары
        if ($user->isUserRoleMentor()) {
            /** @var EmployeeMentor $employeeMentor */
            $employeeMentor = EmployeeMentor::find()->andWhere(['created_by' => $authUser->user_uuid])
                ->andWhere(['mentor_uuid' => $user->user_uuid])
                ->one();
            return !empty($employeeMentor);
        }

        return false;
    }
}
