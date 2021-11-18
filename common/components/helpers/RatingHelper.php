<?php

namespace common\components\helpers;

use common\access\Rbac;
use common\models\Program;
use common\models\SessionRating;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserProgram;
use common\models\UserTraining;
use yii\db\ActiveQuery;

class RatingHelper
{
    public static function update(SessionRating $rating): void
    {
        /** @var User $ratedUser */
        $ratedUser = $rating->rated;
        if (!$ratedUser) {
            return;
        }

        /** @var User $author */
        $author = $rating->author;
        if (!$author) {
            return;
        }

        /** @var TrainingSession $session */
        $session = $rating->training;

        /** @var User $coachOrMentorUser */
        $coachOrMentorUser = $session->coachOrMentor;
        if (empty($coachOrMentorUser)) {
            return;
        }

        $program_uuid = $coachOrMentorUser->role === Rbac::ROLE_MENTOR ? Program::MENTOR_UUID : Program::COACH_UUID;

        /** @var UserProgram $authorUserProgram */
        $authorUserProgram = $author->getProgramAssignments()
            ->andWhere(['program_uuid' => $program_uuid])
            ->one();

        /** @var UserProgram $ratedUserProgram */
        $ratedUserProgram = $ratedUser->getProgramAssignments()
            ->andWhere(['program_uuid' => $program_uuid])
            ->one();

        if (!$ratedUserProgram) {
            return;
        }

        $role = $program_uuid == Program::COACH_UUID ? 'coach' : 'mentor';

        try {
            // Средняя оценка, которую сотруднику проставили менторы, которые с ним занимались
            if ($author->isUserRoleMentor() && $ratedUser->isUserRoleEmployee()) {
                /** @var ActiveQuery $sessionsQuery */
                $avg = self::getSessionRatingAvg($ratedUser->user_uuid, $role);
                $ratedUserProgram->session_rating_avg = !empty($avg) ? round($avg, 1) : 0;
            }

            // Средняя оценка, которую сотрудник проставил коучам с которыми занимался
            if ($author->isUserRoleEmployee() && $ratedUser->isUserRoleCoach()) {
                /** @var ActiveQuery $sessionsQuery */
                $avg = self::getCouchOrMentorRatingAvg($author->user_uuid, $role);
                $authorUserProgram->couch_rating_avg = !empty($avg) ? round($avg, 1) : 0;
                $ratedUserProgram->couch_rating_avg = !empty($avg) ? round($avg, 1) : 0;
            }

            // Средняя оценка, которую сотрудник проставил тренерам с которыми занимался
            if ($author->isUserRoleEmployee() && $ratedUser->isUserRoleMentor()) {
                /** @var ActiveQuery $sessionsQuery */
                $avg = self::getCouchOrMentorRatingAvg($author->user_uuid, $role);
                $authorUserProgram->mentor_rating_avg = !empty($avg) ? round($avg, 1) : 0;
                $ratedUserProgram->mentor_rating_avg = !empty($avg) ? round($avg, 1) : 0;
            }

            $authorUserProgram->save();
            $ratedUserProgram->save();

            $rating->toCalculate();

            $rating->save(false);
        } catch (\PDOException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }

    /**
     * @param string $userUuid
     * @param string $role
     * @return float
     */
    private static function getSessionRatingAvg(string $userUuid, string $role): float
    {
        $sessionIds = self::getSessionIds($userUuid, $role);

        $sessionRatingsQuery = SessionRating::find()
            ->andWhere([SessionRating::tableName() . '.user_uuid' => $userUuid])
            ->joinWith('training')
            ->andWhere(['in', TrainingSession::tableName() . '.training_uuid', $sessionIds]);

        return $sessionRatingsQuery->average('rate');
    }

    /**
     * @param string $userUuid
     * @param string $role
     * @return mixed
     */
    private static function getCouchOrMentorRatingAvg(string $userUuid, string $role): float
    {
        $sessionIds = self::getSessionIds($userUuid, $role);

        $sessionRatingsQuery = SessionRating::find()
            ->andWhere([SessionRating::tableName() . '.created_by' => $userUuid])
            ->joinWith('training')
            ->andWhere(['in', TrainingSession::tableName() . '.training_uuid', $sessionIds]);

        return $sessionRatingsQuery->average('rate');
    }

    /**
     * @param string $userUuid
     * @param string $role
     * @return array
     */
    private static function getSessionIds(string $userUuid, string $role): array
    {
        return TrainingSession::find()
            ->joinWith('userAssignments.user')
            ->andWhere(['in', TrainingSession::tableName(). '.status', [TrainingSession::STATUS_COMPLETED, TrainingSession::STATUS_RATED]])
            ->andWhere(['!=', UserTraining::tableName() . '.user_uuid', $userUuid])
            ->andWhere([User::tableName() . '.role' => $role])
            ->select(TrainingSession::tableName() . '.training_uuid')
            ->column();
    }
}
