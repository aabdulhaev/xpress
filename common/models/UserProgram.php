<?php

namespace common\models;

use common\access\Rbac;
use common\models\queries\UserProgramQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_program".
 *
 * @property string $user_uuid
 * @property string $program_uuid
 * @property int $created_at
 * @property int $session_planed
 * @property int $session_appointed_to_employee
 * @property int $client_session_complete
 * @property int $session_complete
 * @property float|null $session_rating_avg
 * @property float|null $couch_rating_avg
 * @property float|null $mentor_rating_avg
 * @property float|null $session_cancel
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $status
 *
 * @property Program $program
 * @property User $user
 */
class UserProgram extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_program';
    }

    /**
     * {@inheritdoc}
     * @return UserProgramQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserProgramQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uuid' => 'User Uuid',
            'program_uuid' => 'Program Uuid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
            'session_rating_avg' => 'Средняя оценка, которую сотруднику проставили менторы, которые с ним занимались',
            'couch_rating_avg' => 'Средняя оценка, которую сотрудник проставил коучам с которыми занимался',
            'mentor_rating_avg' => 'Средняя оценка, которую сотрудник проставил тренерам с которыми занимался',
            'couch_and_mentor_rating_avg' => 'Средняя оценка, которую проставили коучу или ментору сотрудники, которые с ним занимались',
            'client_session_complete' => 'завершено сессий внутри компании (для коуча) - Сессии',
            'session_complete' => 'завершено сессий - Всего',
            'session_appointed_to_employee' => 'количество сессий, назначенных HR для сотрудника (для hr)'
        ];
    }

    public static function create($program_uuid, $session_planed = 0): self
    {
        $assignment = new static();
        $assignment->program_uuid = $program_uuid;
        $assignment->session_planed = $session_planed;
        return $assignment;
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function getProgram() : ActiveQuery
    {
        return $this->hasOne(Program::class,['program_uuid' => 'program_uuid']);
    }

    public function getUser() : ActiveQuery
    {
        return $this->hasOne(User::class,['user_uuid' => 'user_uuid']);
    }

    public function getTrainingAssignments(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['user_uuid' => 'user_uuid']);
    }

    public function fields(): array
    {
        /** @var User $authUser */
        $authUser = \Yii::$app->user->identity;
        /** @var User $user */
        $user = $this->getUser()->one();

        return [
            'program_uuid',
            'program_title' => function(self $model) {
                return $model->program->name;
            },
            'session_planed',

            // количество сессий, назначенных HR для сотрудника (для hr)
            'session_appointed_to_employee' => function(self $model) use ($authUser, $user) {
                return $model->getSessionAppointedToEmployee($authUser, $user);
            },

            //завершено сессий внутри компании (для коуча) - Сессии
            'client_session_complete' => function(self $model) use ($authUser, $user) {
                return $model->getCoachClientSessionComplete($authUser, $user);
            },
            // завершено сессий - Всего
            'session_complete',

            // Средняя оценка, которую сотруднику проставили менторы, которые с ним занимались
            'session_rating_avg',

            // Средняя оценка, которую сотрудник проставил коучам, с которыми занимался
            'couch_rating_avg',

            // Средняя оценка, которую сотрудник проставил тренерам, с которыми занимался
            'mentor_rating_avg',

            // Средняя оценка, которую сотрудники проставили коучу или ментору, которые с ним занимались
            'couch_and_mentor_rating_avg' => function(self $model) use ($user) {
                return $model->getCouchAndMentorRatingAvg($user);
            },

            'session_cancel'
        ];
    }

    /**
     * @param int $count
     */
    public function setSessionComplete(int $count)
    {
        $this->session_complete = $count;
    }

    /**
     * Количество сессий, назначенных HR для сотрудника (для hr)
     * @param User $authUser
     * @param User $user
     * @return int|null
     */
    private function getSessionAppointedToEmployee(User $authUser, User $user): ?int
    {
        if ($authUser->isUserRoleHr() && $user->isUserRoleEmployee()) {
            /** @var ActiveQuery $query */
            $query = TrainingSession::find()->joinWith('userAssignments')
                ->andWhere(['user_training.user_uuid' => $user->user_uuid]);

            return intval($query->count());
        }

        return null;
    }

    /**
     * Количество завершенных сессий коуча внутри компании
     * @param User $authUser
     * @param User $user
     * @return int|null
     */
    private function getCoachClientSessionComplete(User $authUser, User $user): ?int
    {
        if ($authUser->isUserRoleHr() && $user->isUserRoleCoach()) {

            $employeeUserUuids = User::find()->andWhere([User::tableName() . '.client_uuid' => $authUser->client_uuid])
                ->andWhere([User::tableName() . '.role' => Rbac::ROLE_EMP])
                ->groupBy(User::tableName() . '.user_uuid')
                ->select(User::tableName() . '.user_uuid')
                ->column();

            $sessionUuids = TrainingSession::find()->joinWith('userAssignments')
                ->andWhere([
                    'and',
                    ['in', TrainingSession::tableName() . '.status', [TrainingSession::STATUS_COMPLETED, TrainingSession::STATUS_RATED]],
                    ['in', UserTraining::tableName() . '.user_uuid', $employeeUserUuids]
                ])
                ->groupBy(TrainingSession::tableName() . '.training_uuid')
                ->select(TrainingSession::tableName() . '.training_uuid')
                ->column();

            $query = TrainingSession::find()
                ->andWhere(['in', TrainingSession::tableName() . '.training_uuid', $sessionUuids])
                ->joinWith('userAssignments')
                ->andWhere([UserTraining::tableName() . '.user_uuid' => $user->user_uuid])
                ->groupBy(TrainingSession::tableName() . '.training_uuid');

            return intval($query->count());
        }

        return null;
    }

    /**
     * Средняя оценка, которую сотрудники проставили коучу или ментору, которые с ним занимались
     * @param User $user
     * @return float|null
     */
    private function getCouchAndMentorRatingAvg(User $user): ?float
    {
        if ($user->isUserRoleCoach() || $user->isUserRoleMentor()) {
            /** @var ActiveQuery $sessionsQuery */
            $sessionsQuery = SessionRating::find()->joinWith('training')
                ->andWhere(['session_rating.user_uuid' => $user->user_uuid]);
            $avg = $sessionsQuery->average('rate');

            return !empty($avg) ? round($avg, 1) : 0;
        }

        return null;
    }
}
