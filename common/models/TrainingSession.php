<?php

namespace common\models;

use common\access\Rbac;
use common\forms\training\TrainingCreateForm;
use common\forms\training\TrainingEditForm;
use common\forms\training\TrainingRejectMoveRequestForm;
use common\forms\TrainingRatingForm;
use common\models\events\CancelSession;
use common\models\events\CancelSessionNotification;
use common\models\events\ConfirmSession;
use common\models\events\EmployeeCreateSessionPlanning;
use common\models\events\MoveSessionRequest;
use common\models\events\RejectedMoveSessionRequest;
use common\models\events\Stats;
use common\models\events\UserSessionRating;
use common\models\queries\TrainingSessionQuery;
use common\models\traits\AggregateRoot;
use common\models\traits\EventTrait;
use DateTime;
use Exception;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "training_session".
 *
 * @property string $training_uuid
 * @property int $status
 * @property string $subject_uuid
 * @property string $program_uuid
 * @property string $start_at_tc
 * @property int|null $duration
 * @property string|null $service_link
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property SessionRating[] $sessionRatings
 * @property UserTraining[] $userAssignments
 * @property User $coachOrMentor
 * @property User $employee
 * @property Meeting $meeting
 * @property Program $program
 * @property Uuid $moved_from
 * @property string $move_request_reject_comment
 * @property string $moved_by_role
 */
class TrainingSession extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public const STATUS_DELETED = 0;
    public const STATUS_FREE = 1;
    public const STATUS_MOVED = 2;
    public const STATUS_NOT_CONFIRM = 5;
    public const STATUS_CONFIRM = 10;
    public const STATUS_CANCEL = 15;
    public const STATUS_PRESTART = 16;
    public const STATUS_STARTED = 17;
    public const STATUS_COMPLETED = 20;
    public const STATUS_RATED = 25;


    public static function statuses(): array
    {
        return [
            self::STATUS_DELETED => 'Удалено',
            self::STATUS_FREE => 'Свободное время',
            self::STATUS_MOVED => 'Перемещён',
            self::STATUS_NOT_CONFIRM => 'Не подтвержден',
            self::STATUS_CONFIRM => 'Подтвержден',
            self::STATUS_CANCEL => 'Отмена',
            self::STATUS_PRESTART => 'Скоро начнется',
            self::STATUS_STARTED => 'Начался',
            self::STATUS_COMPLETED => 'Завершен',
            self::STATUS_RATED => 'Оценен',
        ];
    }

    public static function colors(): array
    {
        return [
            self::STATUS_DELETED => '#000',
            self::STATUS_FREE => '#00E676',
            self::STATUS_MOVED => '#00E676',
            self::STATUS_NOT_CONFIRM => '#C62828',
            self::STATUS_CONFIRM => '#2196F3',
            self::STATUS_PRESTART => '#2196F3',
            self::STATUS_STARTED => '#2196F3',
            self::STATUS_CANCEL => '#E0E0E0',
            self::STATUS_COMPLETED => '#C62828',
            self::STATUS_RATED => '#212663'
        ];
    }

    public function isFree(): bool
    {
        return $this->status === static::STATUS_FREE;
    }

    /**
     * Проверка есть ли у сессии запрос на перенос времени
     * @return bool
     */
    public function isMoved(): bool
    {
        return $this->status === static::STATUS_MOVED;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%training_session}}';
    }

    /**
     * {@inheritdoc}
     * @return TrainingSessionQuery the active query used by this AR class.
     */
    public static function find(): TrainingSessionQuery
    {
        return new TrainingSessionQuery(static::class);
    }

    public function behaviors(): array
    {
        return [
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'relation' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['sessionRatings', 'userAssignments']
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool|void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            if (isset($changedAttributes['status']) && $this->isStatusCompleted()) {
                $this->setSessionComplete();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'training_uuid' => 'Training Uuid',
            'status' => 'Status',
            'subject_uuid' => 'Subject Uuid',
            'start_at_ts' => 'Start At',
            'duration' => 'Duration',
            'service_link' => 'Service Link',
            'moved_from' => 'Сессия которая была перемещена',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public function fields(): array
    {
        return [
            'training_uuid',
            'status' =>static function(self $model){
                if ($model->isNotConfirm() && $model->isExpired()) {
                    return self::STATUS_CANCEL;
                }

                return $model->status;
            },
            'subject',
            'start_at_tc',
            'start_at' => static function (self $model) {
                return $model->getNormalizedStartTime();
            },
            'end_at' => static function (self $model) {
                return $model->getNormalizedEndTime();
            },
            'duration',
            'service_link',
            'ratings' => static function (self $model) {
                return $model->sessionRatings;
            },
            'employee',
            'coach' => static function (self $model) {
                return $model->coachOrMentor;
            },
            'color' => function (self $model) {
                /** @var User $user */
                $user = Yii::$app->user->identity;
                if (!empty($user)) {
                    return $model->colorCalculate($user);
                } else {
                    return $model->isPast() ? $model::colors()[self::STATUS_NOT_CONFIRM] : $model::colors()[$model->status];
                }

            },
            'is_past' => static function (self $model) {
                return $model->isPast();
            },
            'coach_state' => static function (self $model) {
                return $model->getUserAssignments()
                    ->joinWith('user')
                    ->andWhere(['IN', User::tableName() . '.role', [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR]])->one();
            },
            'emp_state' => static function (self $model) {
                return $model->getUserAssignments()
                    ->joinWith('user')
                    ->andWhere(['=', User::tableName() . '.role', Rbac::ROLE_EMP])->one();
            },
            'meeting',
            'moved_from' => function (self $model) {
                return TrainingSession::findOne(['training_uuid' => $model->moved_from]);
            },
            'moved_by_role',
            'joined' => function (self $model) {
                /** @var User $user */
                $user = \Yii::$app->user->identity;
                /** @var Meeting $meeting */
                $meeting = Meeting::find()->andWhere(['training_uuid' => $model->training_uuid])->one();
                $my = false;
                $other = false;
                if (!empty($meeting)) {
                    $usersMeeting = $meeting->getUserMeetings()
                        ->andWhere(['status' => UserMeeting::STATUS_JOINED])
                        ->all();

                    foreach ($usersMeeting as $userMeeting) {
                        /** @var UserMeeting $userMeeting */
                        if ($userMeeting->user_uuid == $user->user_uuid) {
                            $my = true;
                        } else {
                            $other = true;
                        }
                    }
                }

                return [
                    'my' => $my,
                    'other' => $other,
                ];
            },
        ];
    }

    /**
     * @param User $user
     * @return string
     */
    public function colorCalculate(User $user): string
    {
        if (!empty($user) && $this->isStatusCompleted()) {
            /** @var SessionRating $rating */
            $rating = SessionRating::find()->where([
                'training_uuid' => $this->training_uuid,
                'created_by' => $user->user_uuid
            ])->one();
            if (!empty($rating)) {
                return self::colors()[self::STATUS_RATED];
            }
        } elseif ($this->isNotConfirm() && $this->isExpired()) {
            return self::colors()[self::STATUS_CANCEL];
        }

        return self::colors()[$this->status];
    }

    /**
     * @throws Exception
     */
    public function isPast(): bool
    {
        $start = new DateTime($this->getNormalizedEndTime());
        $now = new DateTime();

        return $start < $now;
    }

    /**
     *
     * @return bool
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function isExpired(): bool
    {
        $start = date_create_from_format('Y-m-d H:i:sP', $this->start_at_tc);
        $start->modify('+ ' . \Yii::$app->params['expiredSessionCancelTime'] . ' seconds');
        $expiredTime = Yii::$app->formatter->asDatetime($start->format('U'), 'php:Y-m-d H:i:sP');

        $now = new DateTime();
        $now = Yii::$app->formatter->asDatetime($now->format('U'), 'php:Y-m-d H:i:sP');

        return $now > $expiredTime;
    }

    public static function create(TrainingCreateForm $form): self
    {
        $model = new static();
        $model->training_uuid = Uuid::uuid6();
        $model->start_at_tc = $form->start_at;
        $model->duration = $form->duration;
        $model->subject_uuid = $form->subject_uuid;
        $model->moved_from = $form->moved_from ?? null;
        $model->moved_by_role = $form->moved_by_role ?? null;
        $model->status = $form->scenario === 'free'
            ? TrainingSession::STATUS_FREE
            : TrainingSession::STATUS_NOT_CONFIRM;

        return $model;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getNormalizedStartTime(): string
    {
        return Yii::$app->formatter
            ->asDatetime($this->start_at_tc, 'php:Y-m-d H:i:sP');
    }

    /**
     * @throws InvalidConfigException
     */
    public function getNormalizedEndTime(): ?string
    {
        $date = date_create_from_format('Y-m-d H:i:sP', $this->start_at_tc);
        if (!$date) {
            return $this->start_at_tc;
        }
        $date->modify('+ ' . $this->duration . ' seconds');
        return Yii::$app->formatter->asDatetime($date->format('U'), 'php:Y-m-d H:i:sP');
    }

    public function getNormalizedStartTimeObject(): DateTime
    {
        return date_create_from_format('Y-m-d H:i:sP', $this->start_at_tc);
    }

    public function getNormalizedEndTimeObject(): DateTime
    {
        $date = date_create_from_format('Y-m-d H:i:sP', $this->start_at_tc);
        return $date->modify('+ ' . $this->duration . ' seconds');
    }

    public function getUserAssignments(): ActiveQuery
    {
        return $this->hasMany(UserTraining::class, ['training_uuid' => 'training_uuid']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['user_uuid' => 'user_uuid'])
            ->via('userAssignments');
    }

    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_uuid' => 'user_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('userAssignments');
    }

    public function getCoachOrMentor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_uuid' => 'user_uuid'])
            ->andOnCondition(['IN', 'role', [Rbac::ROLE_MENTOR, Rbac::ROLE_COACH]])
            ->via('userAssignments');
    }

    public function getSessionRatings(): ActiveQuery
    {
        return $this->hasMany(SessionRating::class, ['training_uuid' => 'training_uuid']);
    }

    public function getSubject(): ActiveQuery
    {
        return $this->hasOne(Subject::class, ['subject_uuid' => 'subject_uuid']);
    }

    public function getMeeting(): ActiveQuery
    {
        return $this->hasOne(Meeting::class, ['training_uuid' => 'training_uuid']);
    }

    public function getMovedFrom(): ActiveQuery
    {
        return $this->hasOne(TrainingSession::class, ['training_uuid' => 'moved_from']);
    }

    public function toCancel($form = null): void
    {
        $this->status = static::STATUS_CANCEL;
        $this->recordEvent(new CancelSession($this));

        if (!empty($form) && $this->coachOrMentor && $this->employee) {
            $this->cancelNotification($form);
        }
    }

    public function toConfirm(): void
    {
        $this->status = static::STATUS_CONFIRM;
    }

    public function toDeleted(): void
    {
        $this->status = static::STATUS_DELETED;
    }

    public function sendConfirmNotification($sender)
    {
        $this->recordEvent(new ConfirmSession($this, $sender));
    }

    /**
     * Отправка сообщения при переносе сессии
     * @param TrainingCreateForm $form
     */
    public function sendMoveNotification(TrainingCreateForm $form)
    {
        $this->recordEvent(new MoveSessionRequest($this, $form));
    }

    /**
     * Отправка сообщения об отклонении переноса сессии
     * @param TrainingRejectMoveRequestForm $form
     */
    public function sendRejectedMoveNotification(TrainingRejectMoveRequestForm $form)
    {
        $this->recordEvent(new RejectedMoveSessionRequest($this, $form));
    }

    /**
     * Отправка сообщения при отмене сессии
     * @param $form TrainingEditForm
     */
    public function cancelNotification(TrainingEditForm $form)
    {
        if ($this->coachOrMentor && $this->employee) {
            $this->recordEvent(new CancelSessionNotification($this, $form));
        }
    }


    public function toNonConfirm(): void
    {
        $this->status = static::STATUS_NOT_CONFIRM;
    }

    public function toComplete(): void
    {
        $this->status = static::STATUS_COMPLETED;
    }

    public function toPrestart(): void
    {
        $this->status = static::STATUS_PRESTART;
    }

    public function toStart(): void
    {
        $this->status = static::STATUS_STARTED;
    }

    public function toRated(): void
    {
        if (count($this->sessionRatings) === 2) {
            $this->status = static::STATUS_RATED;
        }
    }

    public function move(string $start_at, int $duration): void
    {
        $this->start_at_tc = $start_at;
        $this->duration = $duration;
    }

    public function assignRating($user_uuid, $rate, $comment, $subjects): void
    {
        $assignments = $this->sessionRatings;
        foreach ($assignments as $assignment) {
            if ($assignment->isForUser($user_uuid)) {
                return;
            }
        }
        /** @var SessionRating $newRate */
        $newRate = SessionRating::create($user_uuid, $rate, $comment, $subjects);
        $assignments[] = $newRate;
        $this->sessionRatings = $assignments;
        $this->recordEvent(new Stats($newRate));
    }

    public function assignMember(UserTraining $userAssignment): void
    {
        $assignments = $this->userAssignments;
        foreach ($assignments as $assignment) {
            if ($assignment->isForUser($userAssignment->user_uuid)) {
                return;
            }
        }
        $assignments[] = $userAssignment;
        $this->userAssignments = $assignments;
    }

    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['program_uuid' => 'program_uuid']);
    }

    /**
     * @param $form TrainingRatingForm
     */
    public function sendEmailRate(TrainingRatingForm $form): void
    {
        $this->recordEvent(new UserSessionRating($this, $form));
    }

    public function sendCreateSessionPlanning()
    {
        $this->recordEvent(new EmployeeCreateSessionPlanning($this));
    }

    public function delete()
    {
        return $this->softDelete();
    }

    /**
     * @return bool
     */
    public function isConfirm(): bool
    {
        return $this->status == static::STATUS_CONFIRM;
    }

    /**
     * @return bool
     */
    public function isNotConfirm(): bool
    {
        return $this->status == static::STATUS_NOT_CONFIRM;
    }

    /**
     * @return bool
     */
    public function isStatusFree(): bool
    {
        return $this->status == static::STATUS_FREE;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted(): bool
    {
        return $this->status == static::STATUS_COMPLETED;
    }

    public function setSessionComplete()
    {
        /** @var User $coachOrMentorUser */
        $coachOrMentorUser = $this->coachOrMentor;
        /** @var User $employeeUser */
        $employeeUser = User::find()->joinWith('trainingAssignments')
            ->andWhere(
                ['AND',
                    [UserTraining::tableName() . '.training_uuid' => $this->training_uuid],
                    ['!=', UserTraining::tableName() . '.user_uuid', $coachOrMentorUser->user_uuid]
                ])
            ->one();
        if (empty($coachOrMentorUser) || empty($employeeUser)) {
            return;
        }

        $program_uuid = $coachOrMentorUser->isUserRoleMentor() ? Program::MENTOR_UUID : Program::COACH_UUID;

        /** @var UserProgram $coachOrMentorProgram */
        $coachOrMentorProgram = $coachOrMentorUser->getProgramAssignments()
            ->andWhere(['program_uuid' => $program_uuid])
            ->one();

        /** @var UserProgram $employeeProgram */
        $employeeProgram = $employeeUser->getProgramAssignments()
            ->andWhere(['program_uuid' => $program_uuid])
            ->one();

        if (empty($coachOrMentorProgram) || empty($employeeProgram)) {
            return;
        }

        // количество завершенных сессий коуча или ментора
        $couchOrMentorSessionCompletedCount = $coachOrMentorUser->findCoachOrMentorSessionsByProgram()->count();
        $coachOrMentorProgram->setSessionComplete(intval($couchOrMentorSessionCompletedCount));
        $coachOrMentorProgram->save();

        // количество завершенных сессий сотрудника
        $employeeSessionCompletedCount = $employeeUser->findEmployeeSessionsByProgram($program_uuid)->count();
        $employeeProgram->setSessionComplete(intval($employeeSessionCompletedCount));
        $employeeProgram->save();
    }

    /**
     * @param int $hours
     * @return bool
     * @throws InvalidConfigException
     */
    public function checkTimeToSendNotificationBeforeTrainingSession(int $hours): bool
    {
        $dateFrom = date_create_from_format('Y-m-d H:i:sP', $this->start_at_tc);
        $dateFrom->modify('- ' . $hours . ' hours');
        $startAtFrom = Yii::$app->formatter->asDatetime($dateFrom->format('U'), 'php:Y-m-d H:i:sP');

        $dateTo = date_create_from_format('Y-m-d H:i:sP', $this->start_at_tc);
        $dateTo->modify('- ' . ($hours * 59 * 60) . ' seconds');
        $startAtTo = Yii::$app->formatter->asDatetime($dateTo->format('U'), 'php:Y-m-d H:i:sP');

        $now = new DateTime();
        $now = Yii::$app->formatter->asDatetime($now->format('U'), 'php:Y-m-d H:i:sP');

        return ($now >= $startAtFrom && $now <= $startAtTo);
    }
}
