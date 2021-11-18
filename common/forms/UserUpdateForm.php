<?php

namespace common\forms;

use common\access\Rbac;
use common\models\Client;
use common\models\Competence;
use common\models\Program;
use common\models\Section;
use common\models\Subject;
use common\models\User;
use common\models\UserCompetencyProfile;
use common\models\UserProgram;
use common\validators\UuidValidator;
use Ramsey\Uuid\Uuid;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * @OA\Schema()
 */
class UserUpdateForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $first_name;
    /**
     * @OA\Property()
     * @var string
     */
    public $last_name;
    /**
     * @OA\Property()
     * @var string
     */
    public $email;
    /**
     * @OA\Property()
     * @var int
     */
    public $phone;
    /**
     * @OA\Property()
     * @var string
     */
    public $client_uuid;
    /**
     * @OA\Property()
     * @var string
     */
    public $department;
    /**
     * @OA\Property()
     * @var string
     */
    public $position;
    /**
     * @OA\Property(enum={0, 10, 20})
     * @var int
     */
    public $level;
    /**
     * @OA\Property()
     * @var string
     */
    public $certification;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $subjects = [];
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $programs = [];
    /**
     * @OA\Property()
     * @var int
     */
    public $practice_hours;
    /**
     * @OA\Property()
     * @var string
     */
    public $languages;
    /**
     * @OA\Property()
     * @var string
     */
    public $content;
    /**
     * @OA\Property(type="file")
     * @var string
     */
    public $image;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $competencies = [];
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $sections = [];
    /**
     * @OA\Property()
     * @var string
     */
    public $role;

    public $user;

    /** @var User $authUser */
    public $authUser;

    private $user_uuid;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);

        $this->user = $user;

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->role;
        if (!empty($user->client_uuid)) {
            $this->client_uuid = $user->client_uuid;
        }
        $this->phone = $user->phone;
        $this->department = $user->department;
        $this->position = $user->position;
        $this->level = $user->isUserRoleCoach() ? $user->level : User::LEVEL_ACC;
        $this->certification = $user->certification;
        $this->languages = $user->languages;
        $this->practice_hours = $user->practice_hours;
        $this->content = $user->content;

        $this->subjects = ArrayHelper::getColumn($user->subjects, 'subject_uuid');
        $this->competencies = ArrayHelper::getColumn($user->competencies, 'competence_uuid');
        $this->sections = ArrayHelper::getColumn($user->sections, 'section_uuid');

        $this->user_uuid = $user->user_uuid;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique',
                'targetClass' => User::class,
                'message' => 'Этот Email уже используется.',
                'filter' => function (ActiveQuery $query) {
                    return $query->alias('u')
                        ->where(['~*', 'u.email', $this->email])
                        ->andWhere(['!=', 'u.user_uuid', $this->user->user_uuid]);
                }
            ],

            ['first_name', 'trim'],
            ['first_name', 'required'],
            ['first_name', 'string', 'max' => 32],

            ['last_name', 'trim'],
            ['last_name', 'required'],
            ['last_name', 'string', 'max' => 32],

            ['client_uuid', UuidValidator::class],
            ['client_uuid', 'default', 'isEmpty' => function ($value) {
                return empty($value) || $value === null;
            }],
            ['client_uuid', 'required', 'when' => static function (self $model) {
            if ($model->authUser->isUserRoleAdmin()) {
                return false;
            }
                return in_array($model->role, [Rbac::ROLE_EMP, Rbac::ROLE_MENTOR, Rbac::ROLE_HR], true);
            }],
            ['client_uuid', 'string', 'skipOnEmpty' => true],
            ['client_uuid', 'exist',
                'targetClass' => Client::class,
                'filter' => ['status' => Client::STATUS_ACTIVE],
                'message' => 'Компания не найдена'
            ],

            ['department', 'trim'],
            ['department', 'required', 'when' => static function (self $model) {
                return in_array($model->role, [Rbac::ROLE_EMP, Rbac::ROLE_MENTOR], true);
            }],
            ['department', 'string', 'max' => 64],

            ['position', 'trim'],
            ['position', 'required', 'when' => static function (self $model) {
                return in_array($model->role, [Rbac::ROLE_EMP, Rbac::ROLE_MENTOR], true);
            }],
            ['position', 'string', 'max' => 64],

            ['phone', 'integer'],

            ['role', 'required'],
            ['role', 'string', 'max' => 32],
            ['role', 'in', 'range' => [
                Rbac::ROLE_ADMIN,
                Rbac::ROLE_HR,
                Rbac::ROLE_EMP,
                Rbac::ROLE_MENTOR,
                Rbac::ROLE_COACH,
                Rbac::ROLE_MODERATOR,
            ]],

            ['level', 'required', 'when' => function ($model) {
                return $model->role === Rbac::ROLE_COACH;
            }],

            ['certification', 'string', 'max' => 512],
            ['certification', 'required', 'when' => static function (self $model) {
                return $model->role === Rbac::ROLE_COACH;
            }],

            ['subjects', 'each', 'rule' => [UuidValidator::class]],
            ['subjects', 'each', 'rule' => [
                'exist',
                'targetClass' => Subject::class,
                'targetAttribute' => 'subject_uuid'
            ]],

            ['competencies', 'each', 'rule' => [UuidValidator::class]],
            ['competencies', 'each', 'rule' => [
                'exist',
                'targetClass' => Competence::class,
                'targetAttribute' => 'competence_uuid'
            ], 'skipOnEmpty' => true],

            [['subjects', 'competencies'], function ($attribute) {
                if (is_array($this->$attribute) && count($this->$attribute) > 3) {
                    $this->addError($attribute, 'Можно выбрать максимум 3 элемента.');
                }
            }],

            ['sections', 'each', 'rule' => [UuidValidator::class]],
            ['sections', 'each', 'rule' => [
                'exist',
                'targetClass' => Section::class,
                'targetAttribute' => 'section_uuid'
            ]],
            ['sections', 'required', 'when' => static function (self $model) {
                return $model->role === Rbac::ROLE_MODERATOR;
            }],

            ['programs', 'required', 'when' => static function (self $model) {
                return $model->role === Rbac::ROLE_EMP;
            }],
            ['programs', 'each', 'rule' => ['programValidator'], 'skipOnEmpty' => true],

            ['practice_hours', 'default', 'value' => 0, 'isEmpty' => function ($value) {
                return empty($value) || $value === 'null';
            }],
            ['practice_hours', 'integer', 'min' => 0, 'skipOnEmpty' => true],
            ['languages', 'string', 'max' => 255, 'skipOnEmpty' => true],
            ['content', 'string', 'skipOnEmpty' => true],

            [
                'image', 'image',
                'skipOnEmpty' => true,
                'minWidth' => 378,
                'minHeight' => 222,
                'extensions' => 'png, jpg, jpeg',
                'mimeTypes' => 'image/*'
            ],
            ['image', 'required', 'when' => static function (self $model) {
                $required = false;

                if($model->role === Rbac::ROLE_EMP){
                    foreach ($model->programs as $program) {
                        $program_uuid = ArrayHelper::getValue($program, 'program_uuid', '');
                        $enable = ArrayHelper::getValue($program, 'enable', 'false');

                        if($enable == 'true' && $program_uuid == Program::COACH_UUID){
                            /** @var UserCompetencyProfile $userCompetencyProfile */
                            $userCompetencyProfile = UserCompetencyProfile::find()->where(['user_uuid' => $model->user_uuid])
                                ->one();
                            $required = empty($userCompetencyProfile);
                            break;
                        }
                    }
                }

                return $required;
            }]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'client_uuid' => 'Компания',
            'department' => 'Отдел',
            'position' => 'Должность',
            'level' => 'Уровень',
            'certification' => 'Сертификация',
            'subjects' => 'Цели развития',
            'programs' => 'Программы',
            'practice_hours' => 'Часов практики',
            'languages' => 'Языки',
            'content' => 'Примечание',
            'image' => 'Профиль компетенций',
            'competencies' => 'Специализация',
            'sections' => 'Секции',
            'role' => 'Роль',
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->image = UploadedFile::getInstanceByName('image');
            return true;
        }

        return false;
    }

    public function programValidator($attribute)
    {
        $enabled = 0;

        foreach ($this->programs as $program) {
            $program_uuid = ArrayHelper::getValue($program, 'program_uuid', '');
            $session = ArrayHelper::getValue($program, 'session', 0);
            $enable = ArrayHelper::getValue($program, 'enable', 'false');

            if (!Uuid::isValid($program_uuid)) {
                $this->addError('programs', $program_uuid . ' не правильный UUID');
                continue;
            }

            $programExists = Program::find()->andWhere(['program_uuid' => $program_uuid])->exists();
            if (!$programExists) {
                $this->addError('programs', $program_uuid . ' не сушествует');
                continue;
            }

            if ($enable == 'true') {
                /** @var UserProgram $userProgram */
                $userProgram = $this->user->getProgramAssignments()
                    ->andWhere(['program_uuid' => $program_uuid])
                    ->one();
                if (!empty($userProgram) && $userProgram->session_complete > $session) {
                    $this->addError('programs', 'Количество запланированных сессий не должно быть меньше количества завершенных сессий.');
                    return false;
                }

                if ($this->role === Rbac::ROLE_EMP && intval($session) <= 0) {
                    $this->addError('programs', 'Количество сессий должно быть числом больше нуля.');
                }

                $enabled++;
            }
        }

        if ($this->role === Rbac::ROLE_EMP && $enabled == 0) {
            $this->addError('programs', 'Необходимо заполнить программы.');
        }
    }
}
