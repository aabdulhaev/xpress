<?php

namespace common\models;

use common\access\Rbac;
use common\behaviors\uploadBehavior\ImageUploadBehavior;
use common\components\google\Token;
use common\forms\UserCreateForm;
use common\forms\UserUpdateForm;
use common\models\events\PasswordResetRequest;
use common\models\events\UserContact;
use common\models\events\UserInvite;
use common\models\events\UserSignUpRequested;
use common\models\queries\UserSectionQuery;
use common\models\traits\AggregateRoot;
use common\models\traits\EventTrait;
use common\models\traits\JwtTrait;
use common\repositories\ClientRepository;
use DateTimeImmutable;
use DomainException;
use Exception;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * @OA\Schema()
 *
 * @OA\Property (property="user_uuid", type="string")
 * @OA\Property (property="first_name", type="string")
 * @OA\Property (property="last_name", type="string")
 * @OA\Property (property="client_uuid", type="string")
 * @OA\Property (property="client_name", type="string")
 * @OA\Property (property="email", type="string")
 * @OA\Property (property="role", type="string")
 * @OA\Property (property="status", type="integer", enum={0, 5, 10})
 * @OA\Property (property="verification_token", type="string")
 * @OA\Property (property="avatar_50", type="string")
 * @OA\Property (property="avatar_250", type="string")
 * @OA\Property (property="mentor_program", type="boolean")
 * @OA\Property (property="coach_program", type="boolean")
 * @OA\Property (property="inCoachProgram", type="boolean")
 * @OA\Property (property="certification", type="string")
 * @OA\Property (property="position", type="string")
 * @OA\Property (property="department", type="string")
 * @OA\Property (property="time_zone", type="string")
 * @OA\Property (property="level", type="string")
 * @OA\Property (property="levelLabel", type="string")
 * @OA\Property (property="content", type="string")
 * @OA\Property (property="practice_hours", type="string")
 * @OA\Property (property="languages", type="string")
 * @OA\Property (property="coach_video_presentation", type="string")
 *
 * This is the model class for table "user".
 *
 * @property string $user_uuid
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $first_name
 * @property string $last_name
 * @property string $fullName
 * @property string $phone
 * @property int $status
 * @property string $avatar
 * @property string $client_uuid
 * @property string $department
 * @property string $position
 * @property string $level
 * @property string $certification
 * @property string $role
 * @property string $verification_token
 * @property string $time_zone
 * @property string $practice_hours
 * @property string $languages
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 * @property int $blocked_at
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $blocked_by [uuid]
 * @property string $video_presentation_coach_link
 * @property string $google_access_token
 * @property string $google_refresh_token
 * @property int $google_token_created
 * @property int $google_expires_in
 *
 * @property UserStat $stat
 * @property Subject[] $subjects
 * @property Competence[] $competencies
 * @property UserMeeting[] $userMeetings
 *
 * @property UserSubject[] $subjectAssignments
 * @property UserCompetence[] $competenceAssignments
 * @property EmployeeMentor[] $mentorAssignments
 * @property EmployeeMentor[] $employeeAssignments
 * @property ClientCoach[] $clientCoachesAssignments
 * @property UserProgram[] $programAssignments
 * @property UserTraining[] $trainingAssignments
 *
 * @property User[] $mentors
 * @property User[] $approvedMentors
 * @property User[] $notApprovedMentors
 * @property User[] $connectedMentors
 * @property User[] $unconnectedMentors
 * @property User[] $declineMentors
 *
 * @property User[] $coaches
 * @property User[] $approvedCoaches
 * @property User[] $notApprovedCoaches
 * @property User[] $connectedCoaches
 * @property User[] $unconnectedCoaches
 * @property User[] $declineCoaches
 *
 * @property User[] $employees
 * @property User[] $approvedEmployees
 * @property User[] $notApprovedEmployees
 * @property User[] $connectedEmployees
 * @property User[] $unconnectedEmployees
 * @property User[] $declineEmployees
 *
 * @property User[] $clientEmployees
 * @property User[] $clientMentors
 * @property User[] $clientCoaches
 * @property User[] $clientHr
 * @property Client $client
 *
 * @property Program[] $programs
 * @property Program $mentorProgram
 * @property Program $coachProgram
 * @property boolean $inMentorProgram
 * @property boolean $inCoachProgram
 *
 * @property TrainingSession[] $trainings
 * @property UserCompetencyProfile[] $competencyProfiles
 *
 * @property Section[] $sections
 * @property UserSection[] $sectionAssignments
 *
 * @mixin ImageUploadBehavior
 */
class User extends ActiveRecord implements IdentityInterface, AggregateRoot
{
    use EventTrait;
    use JwtTrait;

    public const STATUS_INACTIVE = 0;
    public const STATUS_SUSPENDED = 5;
    public const STATUS_ACTIVE = 10;

    public const LEVEL_ACC = 0;
    public const LEVEL_PCC = 10;
    public const LEVEL_MCC = 20;

    public const SEED_ADMIN_UUID = '1eb0f8a0-5598-6b30-7450-9d60097e9202';

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'ava' => [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'avatar',
                'createThumbsOnRequest' => true,
                'filePath' => '@storageRoot/origin/avatars/[[attribute_id]]/[[pk]].[[extension]]',
                'fileUrl' => '@storage/origin/avatars/[[attribute_id]]/[[pk]].[[extension]]',
                'thumbPath' => '@storageRoot/cache/avatars/[[attribute_id]]/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '@storage/cache/avatars/[[attribute_id]]/[[profile]]_[[pk]].[[extension]]',
                'thumbs' => [
                    'header' => ['width' => 50, 'height' => 50],
                    'profile' => ['width' => 250, 'height' => 250],
                ],
            ],
            'relation' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'trainings',
                    'stat',
                    'subjectAssignments',
                    'programAssignments',
                    'trainingAssignments',
                    'competenceAssignments',
                    'employeeAssignments',
                    'mentorAssignments',
                    'clientCoachesAssignments',
                    'sectionAssignments'
                ]
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_SUSPENDED
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->where(['user_uuid' => $id])
            ->andWhere(['in', 'status', [self::STATUS_ACTIVE, self::STATUS_INACTIVE]])
            ->one();
    }

    public static function findActiveIdentityByEmail($email)
    {
        return static::find()
            ->where(['~*', 'email', $email])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Статусы активности пользователя
     *
     * @return string[]
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_INACTIVE => 'Неактивен',
            self::STATUS_SUSPENDED => 'Отключен',
            self::STATUS_ACTIVE => 'Активен',
        ];
    }

    /**
     * Роли пользователей
     *
     * @return string[]
     */
    public static function roles(): array
    {
        return [
            Rbac::ROLE_ADMIN => 'администратор',
            Rbac::ROLE_HR => 'hr менеджер',
            Rbac::ROLE_EMP => 'сотрудник',
            Rbac::ROLE_COACH => 'тренер',
            Rbac::ROLE_MENTOR => 'наставник',
        ];
    }

    /**
     * Уровни пользователя
     *
     * @return string[]
     */
    public static function levels(): array
    {
        return [
            static::LEVEL_ACC => 'ACC',
            static::LEVEL_PCC => 'PCC',
            static::LEVEL_MCC => 'MCC',
        ];
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLevelLabel()
    {
        $array = self::levels();
        return ArrayHelper::getValue($array, $this->level);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getRoleLabel()
    {
        $array = Rbac::rolesTitle();
        return ArrayHelper::getValue($array, $this->role);
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'is_admin' => 'Админ',
        ];
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param string $exp
     * @return string
     * @throws Exception
     */
    public function getLongToken(string $exp = '+1 month'): string
    {
        $now = new DateTimeImmutable();
        return $this->getJWT(
            'user_uuid',
            [
                'exp' => $now->modify($exp)->format('U')
            ]
        );
    }

    /**
     * @param string $exp
     * @return string
     * @throws Exception
     */
    public function getTempToken(string $exp = '+5 minutes'): string
    {
        $now = new DateTimeImmutable();
        return $this->getJWT(
            'user_uuid',
            [
                'exp' => $now->modify($exp)->format('U')
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function fields(): array
    {
        /** @var User $authUser */
        $authUser = \Yii::$app->user->identity;

        return [
            'user_uuid',
            'first_name',
            'last_name',
            'client_uuid',
            'client_name' => static function (self $model) {
                $name = null;
                $clientRep = new ClientRepository();
                $client = $clientRep->find($model->client_uuid);
                if ($client) {
                    $name = $client->name;
                }
                return $name;
            },
            'email',
            'role',
            'status',
            'verification_token',
            'programs' => static function (self $user) use ($authUser) {
                /** @var UserProgram $programAssignment */
                $programAssignment = $user->getProgramAssignment($user, $authUser);
                return !empty($programAssignment) ? [$programAssignment] : $user->programAssignments;
            },
            'avatar_50' => static function (self $user) {
                return $user->getAvatar() . '?v=' . time();
            },
            'avatar_250' => static function (self $user) {
                return $user->getAvatar('profile') . '?v=' . time();
            },
            'subjects' => static function (self $user) {
                return $user->subjects;
            },
            'competencies' => static function (self $user) {
                return $user->competencies;
            },
            'mentor_program' => static function (self $user) use ($authUser) {
                return (!empty($authUser) && ($authUser->isUserRoleCoach()) && ($user->isUserRoleEmployee())) ? false : $user->inMentorProgram;
            },
            'coach_program' => static function (self $user) use ($authUser) {
                return (!empty($authUser) && ($authUser->isUserRoleMentor()) && ($user->isUserRoleEmployee())) ? false : $user->inCoachProgram;
            },
            'inCoachProgram',
            'certification',
            'position',
            'department',
            'time_zone',
            'subjects',
            'level',
            'levelLabel',
            'content',
            'practice_hours',
            'languages',
            'coach_video_presentation' => static function (self $user) {
                if ($user->isUserRoleCoach()) {
                    $path = Yii::getAlias('@storageRoot/origin/video-presentation/' . $user->user_uuid . '/' . $user->user_uuid . '.mp4');
                    if (file_exists($path)) {
                        return Yii::getAlias('@storage/origin/video-presentation/' . $user->user_uuid . '/' . $user->user_uuid) . '.mp4?' . time();
                    }
                }

                return null;
            }
        ];
    }

    public function extraFields(): array
    {
        return [
            'competencyProfiles',
            'sections',
            'client',
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken(string $token): ?User
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()
            ->andWhere(['password_reset_token' => $token])
            ->andWhere(['!=', 'status', static::STATUS_SUSPENDED])
            ->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    private function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws \yii\base\Exception
     */
    private function setAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws \yii\base\Exception
     */
    private function setVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws \yii\base\Exception
     */
    public function requestPasswordReset(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();

        $this->recordEvent(new PasswordResetRequest($this));
    }

    /**
     * @throws \yii\base\Exception
     */
    public function resetPassword($password): void
    {
        if (empty($this->password_reset_token)) {
            throw new DomainException('Password resetting is not requested.');
        }
        $this->setPassword($password);
        $this->password_reset_token = null;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function updatePassword($password): void
    {
        $this->setPassword($password);
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isArchive(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * @param UserCreateForm $dto
     * @return static
     * @throws \yii\base\Exception
     */
    public static function create(UserCreateForm $dto): self
    {
        $password = Yii::$app->security->generateRandomString(8);

        $model = new static();

        $model->setPassword($password);
        $model->setAuthKey();
        $model->setVerificationToken();

        $model->role = $dto->role;
        $model->first_name = $dto->first_name;
        $model->last_name = $dto->last_name;
        $model->email = $dto->email;
        $model->phone = $dto->phone;
        $model->department = $dto->department;
        $model->position = $dto->position;
        $model->client_uuid = $dto->client_uuid;
        $model->user_uuid = Uuid::uuid6();
        $model->status = static::STATUS_INACTIVE;
        $model->level = $dto->level;
        $model->certification = $dto->certification;
        $model->languages = $dto->languages;
        $model->practice_hours = $dto->practice_hours;
        $model->content = $dto->content;

        $model->recordEvent(new UserSignUpRequested($model, $password));
        return $model;
    }

    public function edit(UserUpdateForm $dto): self
    {
        $model = $this;

        $model->first_name = $dto->first_name;
        $model->last_name = $dto->last_name;
        $model->email = $dto->email;
        $model->phone = $dto->phone;
        $model->department = $dto->department;
        $model->position = $dto->position;
        if (!empty($dto->client_uuid)) {
            $model->client_uuid = $dto->client_uuid;
        }
        $model->level = $dto->level;
        $model->certification = $dto->certification;
        $model->languages = $dto->languages;
        $model->practice_hours = $dto->practice_hours;
        $model->content = $dto->content;
        return $model;
    }

    public function confirmSignup(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('Пользователь уже активирован.');
        }
        $this->status = self::STATUS_ACTIVE;
        $this->verification_token = null;
    }

    public function editAvatar($file): void
    {
        $this->removeAvatar();
        $this->avatar = $file;
    }

    public function removeAvatar(): void
    {
        $this->cleanFiles();
        $this->avatar = null;
    }

    public function getAvatar($profile = 'header'): ?string
    {
        return $this->getThumbFileUrl(
            'avatar',
            $profile,
            Url::to('/images/no-avatar-300.png', true)
        );
    }

    public function deleteAvatar()
    {
        $this->cleanFiles();
        $this->updateAttributes(['avatar' => null]);
    }

    public function editStatus(int $status): void
    {
        $this->status = $status;
    }

    public function assignSubject($subject_uuid): void
    {
        $assignments = $this->subjectAssignments;
        foreach ($assignments as $assignment) {
            if ($assignment->subject_uuid == $subject_uuid) {
                return;
            }
        }
        $assignments[] = UserSubject::create($subject_uuid);
        $this->subjectAssignments = $assignments;
    }

    public function revokeSubject($subject_uuid): void
    {
        $assignments = $this->subjectAssignments;
        foreach ($assignments as $i => $assignment) {
            if ($assignment->subject_uuid == $subject_uuid) {
                unset($assignments[$i]);
                $this->subjectAssignments = $assignments;
                return;
            }
        }
        throw new DomainException('Assignment is not found.');
    }

    public function revokeSubjects(): void
    {
        $this->subjectAssignments = [];
    }

    public function assignSection($section_uuid): void
    {
        $assignments = $this->sectionAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->section_uuid == $section_uuid) {
                $new = false;

                if (!$assignment->isActive()) {
                    $assignment->status = UserSection::STATUS_ACTIVE;
                    $assignment->save();
                }
            }
        }

        if ($new) {
            $assignments[] = UserSection::create($section_uuid);
        }

        $this->sectionAssignments = $assignments;
    }

    public function revokeSection($section_uuid): void
    {
        $assignments = $this->sectionAssignments;
        foreach ($assignments as $i => $assignment) {
            if ($assignment->section_uuid == $section_uuid) {
                unset($assignments[$i]);
                $this->sectionAssignments = $assignments;
                return;
            }
        }
        throw new DomainException('Assignment is not found.');
    }

    public function revokeSections(): void
    {
        $this->sectionAssignments = [];
    }

    public function assignProgram($program_uuid, $program_session = 0): void
    {
        $assignments = $this->programAssignments;
        foreach ($assignments as $assignment) {
            if ($assignment->program_uuid === $program_uuid) {
                $assignment->session_planed = $program_session;
                $assignment->save();
                return;
            }
        }
        $assignments[] = UserProgram::create($program_uuid, $program_session);
        $this->programAssignments = $assignments;
    }

    public function revokeProgram($program_uuid): void
    {
        $assignments = $this->programAssignments;
        foreach ($assignments as $i => $assignment) {
            if ($assignment->program_uuid === $program_uuid) {
                unset($assignments[$i]);
                $this->programAssignments = $assignments;
                return;
            }
        }
        throw new DomainException('Assignment is not found.');
    }

    public function revokePrograms(): void
    {
        $this->programAssignments = [];
    }

    public function assignCompetence($competence_uuid): void
    {
        $assignments = $this->competenceAssignments;
        foreach ($assignments as $assignment) {
            if ($assignment->competence_uuid == $competence_uuid) {
                return;
            }
        }
        $assignments[] = UserCompetence::create($competence_uuid);
        $this->competenceAssignments = $assignments;
    }

    public function revokeCompetence($competence_uuid): void
    {
        $assignments = $this->competenceAssignments;
        foreach ($assignments as $i => $assignment) {
            if ($assignment->competence_uuid == $competence_uuid) {
                unset($assignments[$i]);
                $this->competenceAssignments = $assignments;
                return;
            }
        }
        throw new DomainException('Assignment is not found.');
    }

    public function revokeCompetencies(): void
    {
        $this->competenceAssignments = [];
    }

    public function assignStat(): void
    {
        $this->stat = UserStat::create($this->user_uuid);
    }

    public function getStat(): ActiveQuery
    {
        return $this->hasOne(UserStat::class, ['user_uuid' => 'user_uuid']);
    }

    public function getSubjectAssignments(): ActiveQuery
    {
        return $this->hasMany(UserSubject::class, ['user_uuid' => 'user_uuid']);
    }

    public function getSubjects(): ActiveQuery
    {
        return $this->hasMany(Subject::class, ['subject_uuid' => 'subject_uuid'])
            ->via('subjectAssignments');
    }

    public function getSectionAssignments(): ActiveQuery
    {
        return $this->hasMany(UserSection::class, ['user_uuid' => 'user_uuid']);
    }

    public function getSections(): ActiveQuery
    {
        return $this->hasMany(Section::class, ['section_uuid' => 'section_uuid'])
            ->via('sectionAssignments', function (UserSectionQuery $relation) {
                return $relation->where([UserSection::tableName() . '.status' => UserSection::STATUS_ACTIVE]);
            });
    }

    public function getCompetenceAssignments(): ActiveQuery
    {
        return $this->hasMany(UserCompetence::class, ['user_uuid' => 'user_uuid']);
    }

    public function getCompetencies(): ActiveQuery
    {
        return $this->hasMany(Competence::class, ['competence_uuid' => 'competence_uuid'])
            ->via('competenceAssignments');
    }

    public function getUserMeetings(): ActiveQuery
    {
        return $this->hasMany(UserMeeting::class, ['user_uuid' => 'user_uuid']);
    }

    public function getMentorAssignments(): ActiveQuery
    {
        return $this->hasMany(EmployeeMentor::class, ['employee_uuid' => 'user_uuid']);
    }

    public function getEmployeeAssignments(): ActiveQuery
    {
        return $this->hasMany(EmployeeMentor::class, ['mentor_uuid' => 'user_uuid']);
    }

    public function revokeEmployees(): void
    {
        $this->employeeAssignments = [];
    }

    public function revokeMentors(): void
    {
        $this->mentorAssignments = [];
    }

    public function getMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR])
            ->via('mentorAssignments');
    }

    public function getNotApprovedMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('em');
                return $q->andOnCondition(['em.status' => EmployeeMentor::STATUS_NOT_APPROVED]);
            });
    }

    public function getApprovedMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('em');
                return $q->andOnCondition(['em.status' => EmployeeMentor::STATUS_APPROVED]);
            });
    }

    public function getConnectedMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition([
                    'IN',
                    'ea.status',
                    [EmployeeMentor::STATUS_NOT_APPROVED, EmployeeMentor::STATUS_APPROVED]
                ]);
            });
    }

    public function getDeclineMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_DECLINE]);
            });
    }

    public function getUnconnectedMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_UNCONNECTED]);
            });
    }

    public function getCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('mentorAssignments');
    }

    public function getClientCoachesAssignments(): ActiveQuery
    {
        return $this->hasMany(ClientCoach::class, ['client_uuid' => 'client_uuid']);
    }

    public function getClientCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'coach_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('clientCoachesAssignments', function (ActiveQuery $query) {
                return $query->andOnCondition(['status' => ClientCoach::STATUS_APPROVED]);
            });
    }

    public function getApprovedCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_APPROVED]);
            });
    }

    public function getNotApprovedCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_NOT_APPROVED]);
            });
    }

    public function getUnconnectedCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_UNCONNECTED]);
            });
    }

    public function getConnectedCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition([
                    'IN',
                    'ea.status',
                    [EmployeeMentor::STATUS_NOT_APPROVED, EmployeeMentor::STATUS_APPROVED]
                ]);
            });
    }

    public function getDeclineCoaches(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_COACH])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_DECLINE]);
            });
    }

    public function getEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'employee_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('employeeAssignments');
    }

    public function getApprovedEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'employee_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('employeeAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_APPROVED]);
            });
    }

    public function getNotApprovedEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'employee_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('employeeAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_NOT_APPROVED]);
            });
    }

    public function getConnectedEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'employee_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('employeeAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition([
                    'IN',
                    'ea.status',
                    [EmployeeMentor::STATUS_NOT_APPROVED, EmployeeMentor::STATUS_APPROVED]
                ]);
            });
    }

    public function getDeclineEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'employee_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('employeeAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_DECLINE]);
            });
    }

    public function getUnconnectedEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'employee_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP])
            ->via('employeeAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_UNCONNECTED]);
            });
    }

    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['client_uuid' => 'client_uuid']);
    }

    public function getClientEmployees(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['client_uuid' => 'client_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_EMP]);
    }

    public function getClientMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['client_uuid' => 'client_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_MENTOR]);
    }

    public function getClientHr(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['client_uuid' => 'client_uuid'])
            ->andOnCondition(['role' => Rbac::ROLE_HR]);
    }

    public function getProgramAssignments(): ActiveQuery
    {
        return $this->hasMany(UserProgram::class, ['user_uuid' => 'user_uuid']);
    }

    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(Program::class, ['program_uuid' => 'program_uuid'])
            ->via('programAssignments');
    }

    public function getMentorProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['program_uuid' => 'program_uuid'])
            ->via('programAssignments', static function (ActiveQuery $query) {
                return $query->andOnCondition(['program_uuid' => Program::MENTOR_UUID]);
            });
    }

    public function getCoachProgram(): ActiveQuery
    {
        return $this->hasOne(Program::class, ['program_uuid' => 'program_uuid'])
            ->via('programAssignments', static function (ActiveQuery $query) {
                return $query->andOnCondition(['program_uuid' => Program::COACH_UUID]);
            });
    }

    public function getInMentorProgram(): bool
    {
        return $this->getMentorProgram()->exists();
    }

    public function getInCoachProgram(): bool
    {
        return $this->getCoachProgram()->exists();
    }

    public function getTrainingAssignments(): ActiveQuery
    {
        return $this->hasMany(UserTraining::class, ['user_uuid' => 'user_uuid']);
    }

    public function getClientTrainingAssignments(): ActiveQuery
    {
        return $this->hasMany(UserTraining::class, ['user_uuid' => 'user_uuid'])
            ->via('clientEmployees');
    }

    public function getTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->via('trainingAssignments');
    }

    public function getClientTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->via('clientTrainingAssignments');
    }

    public function getConfirmedTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_CONFIRM])
            ->via('trainingAssignments');
    }

    public function getClientConfirmedTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_CONFIRM])
            ->via('clientTrainingAssignments');
    }

    public function getCancelTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_CANCEL])
            ->andOnCondition(['!=', 'status', TrainingSession::STATUS_DELETED])
            ->via('trainingAssignments');
    }

    public function getNotDeletedTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['NOT IN', 'status', [
                TrainingSession::STATUS_DELETED,
                TrainingSession::STATUS_CANCEL,
            ]])
            ->via('trainingAssignments');
    }

    public function getCompletedTrainings(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_COMPLETED])
            ->via('trainingAssignments');
    }

    public function getTrainingsNeedSelfRating(): ActiveQuery
    {
        return TrainingSession::find()->joinWith('userAssignments')
            ->andWhere([TrainingSession::tableName() . '.status' => TrainingSession::STATUS_COMPLETED])
            ->andWhere([UserTraining::tableName() . '.user_uuid' => $this->user_uuid])
            ->andWhere([UserTraining::tableName() . '.status' => UserTraining::STATUS_NOT_ESTIMATE]);
        //            ->joinWith('meeting.userMeetings')
        //            ->andWhere([UserMeeting::tableName() . '.status' => UserMeeting::STATUS_JOINED]);
    }

    public function getTrainingsNotConfirm(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_NOT_CONFIRM])
            ->via('trainingAssignments');
    }

    public function getTrainingsNeedSelfConfirm(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_NOT_CONFIRM])
            ->via('trainingAssignments', static function (ActiveQuery $q) {
                return $q->andOnCondition(['status' => UserTraining::STATUS_NOT_CONFIRM]);
            });
    }

    public function getTrainingsWaitConfirm(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_NOT_CONFIRM])
            ->via('trainingAssignments', static function (ActiveQuery $q) {
                return $q->andOnCondition(['status' => UserTraining::STATUS_CONFIRM]);
            });
    }

    public function getActiveCoachesAndMentors(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['user_uuid' => 'mentor_uuid'])
            ->andOnCondition(['IN', 'role', [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR]])
            ->via('mentorAssignments', static function (ActiveQuery $q) {
                $q->alias('ea');
                return $q->andOnCondition(['ea.status' => EmployeeMentor::STATUS_APPROVED]);
            });
    }

    public function getMyCoachesFreeTimeAssignments(): ActiveQuery
    {
        return $this->hasMany(UserTraining::class, ['user_uuid' => 'user_uuid'])
            ->via('activeCoachesAndMentors');
    }

    public function getMyCoachesFreeTraining(): ActiveQuery
    {
        return $this->hasMany(TrainingSession::class, ['training_uuid' => 'training_uuid'])
            ->andOnCondition(['status' => TrainingSession::STATUS_FREE])
            ->via('myCoachesFreeTimeAssignments');
    }

    /**
     * Gets query for [[UserCompetencyProfiles]].
     *
     * @return ActiveQuery
     */
    public function getCompetencyProfiles(): ActiveQuery
    {
        return $this->hasMany(UserCompetencyProfile::class, ['user_uuid' => 'user_uuid']);
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function delete()
    {
        $this->softDelete();
        if (Yii::$app->request->isDelete && !$this->save()) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            // TODO переделать на очистку статов по связанным программам в user_program
            if ($this->stat) {
                $this->stat->delete();
            }
            return true;
        }
        return false;
    }

    public function contact($userTo, $body): void
    {
        $this->recordEvent(new UserContact($this, $userTo, $body));
    }

    public function invite($userTo, $body): void
    {
        $this->recordEvent(new UserInvite($this, $userTo, $body));
    }

    public function connect($userTo): void
    {
        // TODO разобраться в необходимости метода
        $this->recordEvent(new UserConnect($this, $userTo));
    }

    public function manyConnections($userTo): void
    {
        // TODO разобраться в необходимости метода
        $this->recordEvent(new UserManyConnections($this, $userTo));
    }

    /**
     * @return bool
     */
    public function isUserRoleEmployee(): bool
    {
        return $this->role == Rbac::ROLE_EMP;
    }

    /**
     * @return bool
     */
    public function isUserRoleCoach(): bool
    {
        return $this->role == Rbac::ROLE_COACH;
    }

    /**
     * @return bool
     */
    public function isUserRoleMentor(): bool
    {
        return $this->role == Rbac::ROLE_MENTOR;
    }

    /**
     * @return bool
     */
    public function isUserRoleHr(): bool
    {
        return $this->role == Rbac::ROLE_HR;
    }

    /**
     * @return bool
     */
    public function isUserRoleAdmin(): bool
    {
        return $this->role == Rbac::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isUserModerator(): bool
    {
        return $this->role == Rbac::ROLE_MODERATOR;
    }

    /**
     * @param User $authUser
     * @param User $user
     * @return UserProgram|null
     */
    private function getProgramAssignment(User $user, User $authUser): ?UserProgram
    {
        $programAssignments = $user->programAssignments;
        $programUuid = '';
        if (empty($authUser)) {
            return null;
        }
        if (($authUser->isUserRoleCoach() || $authUser->isUserRoleMentor()) && ($user->isUserRoleEmployee())) {
            if ($authUser->isUserRoleCoach()) {
                $programUuid = Program::COACH_UUID;
            }
            if ($authUser->isUserRoleMentor()) {
                $programUuid = Program::MENTOR_UUID;
            }
            foreach ($programAssignments as $programAssignment) {
                if ($programAssignment['program_uuid'] == $programUuid) {
                    return $programAssignment;
                }
            }
        }
        return null;
    }

    /**
     * Проверка, свободно ли время сессии на определенное время старта и длительность
     *
     * @param $startAt
     * @param $duration
     * @param TrainingSession|null $excludedSession
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isSessionTimeFree($startAt, $duration, TrainingSession $excludedSession = null): bool
    {
        $endAt = date_create_from_format('Y-m-d H:i:sP', $startAt);
        $endAt->modify('+ ' . $duration . ' seconds');

        $startAt = date_create_from_format('Y-m-d H:i:sP', $startAt);

        /** @var ActiveQuery $userTrainingsQuery */
        $userTrainingsQuery = UserTraining::find()->joinWith('training')
            ->andWhere([
                'or',
                ['in', TrainingSession::tableName() . '.status', [
                    TrainingSession::STATUS_CONFIRM,
                    TrainingSession::STATUS_PRESTART,
                    TrainingSession::STATUS_STARTED
                ]
                ],
                [
                    'and',
                    ['=', TrainingSession::tableName() . '.status', TrainingSession::STATUS_NOT_CONFIRM],
                    ['in', UserTraining::tableName() . '.status', [UserTraining::STATUS_NOT_CONFIRM, UserTraining::STATUS_CONFIRM]]
                ]
            ])->andWhere([UserTraining::tableName() . '.user_uuid' => $this->user_uuid]);
        if (!empty($excludedSession)) {
            $userTrainingsQuery->andWhere(['!=', TrainingSession::tableName() . '.training_uuid', $excludedSession->training_uuid]);
        }

        return $this->checkFreeTime($userTrainingsQuery, $startAt, $endAt);
    }

    /**
     * @param $startAt
     * @param $duration
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isFreeSessionTimeFree($startAt, $duration): bool
    {
        $endAt = date_create_from_format('Y-m-d H:i:sP', $startAt);
        $endAt->modify('+ ' . $duration . ' seconds');

        $startAt = date_create_from_format('Y-m-d H:i:sP', $startAt);

        /** @var ActiveQuery $userTrainingsQuery */
        $userTrainingsQuery = UserTraining::find()->joinWith('training')
            ->andWhere([TrainingSession::tableName() . '.status' => TrainingSession::STATUS_FREE])
            ->andWhere([UserTraining::tableName() . '.user_uuid' => $this->user_uuid]);

        return $this->checkFreeTime($userTrainingsQuery, $startAt, $endAt);
    }

    /**
     * @param ActiveQuery $userTrainingsQuery
     * @param $startAt
     * @param $endAt
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    private function checkFreeTime(ActiveQuery $userTrainingsQuery, $startAt, $endAt): bool
    {
        foreach ($userTrainingsQuery->each() as $userTraining) {
            /** @var UserTraining $userTraining */

            /** @var TrainingSession $session */
            $session = $userTraining->training;

            $sessionStartTime = $session->getNormalizedStartTimeObject();
            $sessionEndTime = $session->getNormalizedEndTimeObject();

            if (($startAt > $sessionEndTime) ||
                ($endAt < $sessionStartTime)
            ) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getMentorSessionBusyTimeErrorMessage(): string
    {
        return 'У Вас уже запланирована сессия на выбранное время. Пожалуйста, выберите другое время или один из свободных слотов.';
    }

    /**
     * @return string
     */
    public function getMentorFreeSessionBusyTimeErrorMessage(): string
    {
        return 'У Вас уже создан свободный слот на это время.';
    }

    /**
     * @return string
     */
    public function getEmployeeSessionBusyTimeErrorMessage(): string
    {
        return "У Вас уже запланирована сессия на это время. Выберите другой свободный слот.";
    }

    /**
     * @return string
     */
    public function getEmployeeSessionBusyTimeFreeSlotErrorMessage(): string
    {
        return "У Вас уже запланирована сессия на это время. Выберите другой свободный слот.";
    }

    /**
     * @param string $programUuid
     * @throws ForbiddenHttpException
     */
    public function checkingEmployeeForLimitPlannedSession(string $programUuid)
    {
        if ($this->isUserRoleEmployee()) {
            /** @var ActiveQuery $sessionsQuery */
            $sessionsQuery = TrainingSession::find()
                ->andWhere([
                    'not in',
                    TrainingSession::tableName() . '.status',
                    [TrainingSession::STATUS_DELETED, TrainingSession::STATUS_CANCEL]
                ])
                ->joinWith('userAssignments')
                ->andWhere([UserTraining::tableName() . '.user_uuid' => $this->user_uuid]);

            $sessionIds = $sessionsQuery->select(TrainingSession::tableName() . '.training_uuid')->column();

            $role = $programUuid == Program::COACH_UUID ? 'coach' : 'mentor';
            /** @var ActiveQuery $sessionsQuery */
            $sessionsQuery = TrainingSession::find()
                ->andWhere(['in', TrainingSession::tableName() . '.training_uuid', $sessionIds])
                ->joinWith('userAssignments.user')
                ->andWhere(['!=', UserTraining::tableName() . '.user_uuid', $this->user_uuid])
                ->andWhere([User::tableName() . '.role' => $role]);

            /** @var UserProgram $employeeUserProgram */
            $employeeUserProgram = $this->getProgramAssignments()
                ->andWhere(['program_uuid' => $programUuid])
                ->one();

            if (!empty($employeeUserProgram) && ($sessionsQuery->count() >= $employeeUserProgram->session_planed)) {
                throw new ForbiddenHttpException('Достигнут лимит запланированных сессий для сотрудника.');
            }
        }
    }

    public function getGoogleAccessToken(): Token
    {
        return new Token(
            $this->google_access_token,
            $this->google_token_created,
            $this->google_expires_in,
            $this->google_refresh_token
        );
    }

    public function setGoogleAccessToken(Token $accessToken): void
    {
        $this->google_access_token = $accessToken->getAccessToken();
        $this->google_refresh_token = $accessToken->getRefreshToken();
        $this->google_token_created = $accessToken->getCreated();
        $this->google_expires_in = $accessToken->getExpiresIn();
    }

    /**
     * @return ActiveQuery
     */
    public function getMeetings()
    {
        return $this->hasMany(Meeting::class, ['meeting_uuid' => 'meeting_uuid'])
            ->alias('m')
            ->andWhere(['=', 'm.type', Meeting::TYPE_GROUP_MEETING])
            ->andWhere(['!=', 'm.status', Meeting::STATUS_DELETED])
            ->via('userMeetings');
    }

    /**
     * @param $programUuid
     * @return ActiveQuery
     */
    public function findEmployeeSessionsByProgram($programUuid): ActiveQuery
    {
        /** @var ActiveQuery $sessionsQuery */
        $sessionsQuery = TrainingSession::find()
            ->andWhere(['in', TrainingSession::tableName() . '.status', [TrainingSession::STATUS_COMPLETED, TrainingSession::STATUS_RATED]])
            ->joinWith('userAssignments')
            ->andWhere([UserTraining::tableName() . '.user_uuid' => $this->user_uuid]);

        $sessionIds = $sessionsQuery->select(TrainingSession::tableName() . '.training_uuid')->column();

        $role = $programUuid == Program::COACH_UUID ? 'coach' : 'mentor';

        /** @var ActiveQuery $sessionsQuery */
        return TrainingSession::find()
            ->andWhere(['in', TrainingSession::tableName() . '.training_uuid', $sessionIds])
            ->joinWith('userAssignments.user')
            ->andWhere(['!=', UserTraining::tableName() . '.user_uuid', $this->user_uuid])
            ->andWhere([User::tableName() . '.role' => $role]);
    }

    /**
     * @return ActiveQuery
     */
    public function findCoachOrMentorSessionsByProgram(): ActiveQuery
    {
        return TrainingSession::find()
            ->andWhere(['in', TrainingSession::tableName().'.status', [TrainingSession::STATUS_COMPLETED, TrainingSession::STATUS_RATED]])
            ->joinWith('userAssignments')
            ->andWhere([UserTraining::tableName() . '.user_uuid' => $this->user_uuid]);
    }

    /**
     * @return bool
     */
    public function isMeetingCreator(): bool
    {
        return $this->isUserRoleAdmin() || $this->isUserModerator();
    }
}
