<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\UserMeeting;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class UserMeetingRepository
{
    public function save(UserMeeting $model): void
    {
        if (!$model->save()) {
            throw new \RuntimeException('Ошибка сохранения вебинара.');
        }
    }

    /**
     * @param string $userUuid
     * @return ActiveRecord|null
     */
    public function getByUser(string $userUuid): ?ActiveRecord
    {
        return UserMeeting::find()->where(['user_uuid' => $userUuid])->one();
    }

    /**
     * @param string $email
     * @return ActiveRecord
     */
    public function getByEmail(string $email): ?ActiveRecord
    {
        return UserMeeting::find()->where(['email' => $email])->one();
    }

    /**
     * @param string $meetingUuid
     * @param string $userUuid
     * @return ActiveRecord|null
     */
    public function getByMeetingAndUser(string $meetingUuid, string $userUuid): ?ActiveRecord
    {
        return UserMeeting::find()->where(['meeting_uuid' => $meetingUuid])
            ->andWhere(['user_uuid' => $userUuid])
            ->one();
    }

    /**
     * @param string $meetingUuid
     * @param string $email
     * @return ActiveRecord|null
     */
    public function getByMeetingAndEmail(string $meetingUuid, string $email): ?ActiveRecord
    {
        return UserMeeting::find()->where(['meeting_uuid' => $meetingUuid])
            ->andWhere(['email' => $email])
            ->one();
    }

    /**
     * @param string $token
     * @return ActiveRecord|null
     */
    public function getByToken(string $token): ?ActiveRecord
    {
        return UserMeeting::findOne(['token' => $token]);
    }

    /**
     * @param $condition
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function getByCondition($condition): ActiveRecord
    {
        $userMeeting = UserMeeting::find()->andWhere($condition)->one();
        if (empty($userMeeting)) {
            throw new NotFoundHttpException('Участиник вебинара не найден');
        }

        return $userMeeting;
    }
}
