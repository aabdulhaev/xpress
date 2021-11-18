<?php

declare(strict_types=1);

namespace common\forms\meeting;

use common\models\Meeting;
use common\models\User;
use common\validators\DateTimeValidator;
use common\validators\UuidValidator;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class GroupMeetingUpdateForm extends Model
{
    private $meeting;
    /**
     * @OA\Property()
     * @OA\Required()
     * @var string
     */
    public $start_at;
    /**
     * @OA\Property()
     * @OA\Required()
     * @var string
     */
    public $end_at;
    /**
     * @OA\Property()
     * @OA\Required()
     * @var string
     */
    public $title;
    /**
     * @OA\Property()
     * @OA\Required()
     * @var string
     */
    public $description;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     * @OA\Required()
     */
    public $coaches;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $employees;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $emails;

    public function __construct(Meeting $meeting, $config = [])
    {
        parent::__construct($config);

        $this->meeting = $meeting;
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'start_at', 'end_at'], 'required'],
            [['start_at', 'end_at'], DateTimeValidator::class],
            ['title', 'string', 'max' => 32],
            ['description', 'string'],

            ['coaches', 'each', 'rule' => [UuidValidator::class]],
            ['coaches', 'each', 'rule' => [
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'user_uuid'
            ], 'skipOnEmpty' => true],

            ['employees', 'each', 'rule' => [UuidValidator::class]],
            ['employees', 'each', 'rule' => [
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'user_uuid'
            ], 'skipOnEmpty' => true],

            ['emails', 'each', 'rule' => ['email']],
            ['emails', 'checkEmailExist'],

            ['end_at', 'checkDuration'],
            ['start_at', 'checkMeetingTimeIsBusy'],
            [['title'], 'checkEmptyFields']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'start_at' => 'Дата начала',
            'end_at' => 'Дата окончания',
            'title' => 'Тема',
            'description' => 'Описание',
            'coaches' => 'Коучи',
            'employees' => 'Сотрудники',
            'emails' => 'Электронные почты незарегистрированных пользователей'
        ];
    }

    public function checkEmailExist()
    {
        if (!$this->hasErrors()) {
            if (count($this->emails) != count(array_unique($this->emails))) {
                $this->addError('email', 'Введенные электронные почты повторяются.');
                return true;
            }

            $userUuids = [];
            if (!empty($this->coaches)) {
                $userUuids = array_merge($this->coaches, $userUuids);
            }
            if (!empty($this->employees)) {
                $userUuids = array_merge($this->employees, $userUuids);
            }
            if (!empty($userUuids)) {
                $arr = [];
                foreach ($this->emails as $email) {
                    $emailExists = User::find()
                        ->andWhere(['=', 'email', $email])
                        ->andWhere(['in', 'user_uuid', $userUuids])
                        ->exists();
                    if ($emailExists) {
                        $arr[] = $email;
                    }
                }

                if (!empty($arr)) {
                    $emails = implode(', ', $arr);
                    $message = (count($arr) == 1)
                        ? 'Почта ' . $emails . ' уже была добавлена.'
                        : 'Почты ' . $emails . ' уже были добавлены.';
                    $this->addError('email', $message);
                }
            }
        }
    }

    public function checkDuration()
    {
        if (!$this->hasErrors()) {
            $start_at = date_create_from_format('Y-m-d H:i:sP', $this->start_at);
            $start_at->modify('+ 20 minutes');

            $end_at = date_create_from_format('Y-m-d H:i:sP', $this->end_at);

            if ($start_at > $end_at) {
                $this->addError('end_at', 'Продолжительность вебинара не может быть меньше 20 минут');
            }
        }

        return true;
    }

    public function checkMeetingTimeIsBusy()
    {
        if (!$this->hasErrors()) {
            /** @var Meeting $meeting */
            $meetingsQuery = Meeting::find()
                ->andWhere(['=', 'type', Meeting::TYPE_GROUP_MEETING])
                ->andWhere(['!=', 'status', Meeting::STATUS_DELETED]);

            foreach ($meetingsQuery->each() as $meeting) {
                /** @var Meeting $meeting */

                $start_at = $meeting->getNormalizedTimeObject($this->start_at);
                $end_at = $meeting->getNormalizedTimeObject($this->end_at);

                $meetingStartTime = $meeting->getNormalizedTimeObject($meeting->start_at);
                $meetingEndTime = $meeting->getNormalizedTimeObject($meeting->end_at);

                if ($start_at > $meetingEndTime || $end_at < $meetingStartTime || $this->meeting->meeting_uuid == $meeting->meeting_uuid) {
                    continue;
                } else {
                    $this->addError('start_at', 'На это время уже создан групповой вебинар.');
                }
            }
        }

        return true;
    }

    public function checkEmptyFields()
    {
        if (empty($this->coaches) && empty($this->employees) && empty($this->emails)) {
            $this->addError('coaches', 'Нужно добавить участников вебинара.');
            $this->addError('employees', 'Нужно добавить участников вебинара.');
            $this->addError('emails', 'Нужно добавить участников вебинара.');
        }

        return true;
    }

    public function getMeeting()
    {
        return $this->meeting;
    }
}
