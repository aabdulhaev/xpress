<?php

namespace common\models;

use common\models\events\EmployeeCreateConnect;
use common\models\events\EmployeeUnconnectMentor;
use common\models\events\EmployeeUnconnectMentorForEmployee;
use common\models\events\MentorApproveConnectEmployee;
use common\models\events\MentorCancelEmployee;
use common\models\events\MentorUnconnectEmployee;
use common\models\events\MentorUnconnectEmployeeForMentor;
use common\models\queries\EmployeeMentorQuery;
use common\models\traits\AggregateRoot;
use common\models\traits\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "employee_mentor".
 *
 * @property string $employee_uuid
 * @property string $mentor_uuid
 * @property int $status
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property string|null $comment
 * @property User $employee
 * @property User $mentor
 */
class EmployeeMentor extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public const STATUS_UNCONNECTED = 0;
    public const STATUS_NOT_APPROVED = 5;
    public const STATUS_APPROVED = 10;
    public const STATUS_DECLINE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_mentor';
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
                    'status' => self::STATUS_UNCONNECTED
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     * @return EmployeeMentorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmployeeMentorQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employee_uuid' => 'Employee Uuid',
            'mentor_uuid' => 'Mentor Uuid',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
            'comment' => 'Comment'
        ];
    }

    public function fields()
    {
        return [
            'employee_uuid',
            'mentor_uuid',
            'status',
            'comment'
        ];
    }


    public static function create($employee_uuid, $mentor_uuid, $comment = ''): self
    {
        $assignment = new static();
        $assignment->employee_uuid = $employee_uuid;
        $assignment->mentor_uuid = $mentor_uuid;
        $assignment->comment = $comment;
        $assignment->status = self::STATUS_NOT_APPROVED;

        $assignment->recordEvent(new EmployeeCreateConnect($assignment));
        return $assignment;
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function isForEmployee($id): bool
    {
        return $this->employee_uuid === $id;
    }

    public function isForMentor($id): bool
    {
        return $this->mentor_uuid === $id;
    }

    public function isUnconnected()
    {
        return $this->status === self::STATUS_UNCONNECTED;
    }

    public function isNotApproved()
    {
        return $this->status === self::STATUS_NOT_APPROVED;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDecline()
    {
        return $this->status === self::STATUS_DECLINE;
    }

    public function request()
    {
        $this->recordEvent(new EmployeeCreateConnect($this));
    }

    public function approve(): void
    {
        $this->status = static::STATUS_APPROVED;
        $this->recordEvent(new MentorApproveConnectEmployee($this));
    }

    public function decline($comment, $scenario = 'employee'): void
    {
        $this->status = static::STATUS_DECLINE;
        $this->comment = $comment;
        if ($scenario === 'employee') {
            $this->recordEvent(new EmployeeUnconnectMentor($this));
            $this->recordEvent(new EmployeeUnconnectMentorForEmployee($this));
        } else {
            $this->recordEvent(new MentorUnconnectEmployee($this));
            $this->recordEvent(new MentorUnconnectEmployeeForMentor($this));
        }
    }

    public function cancel($comment)
    {
        $this->comment = $comment;

        $this->recordEvent(new MentorCancelEmployee($this));
    }

    public function getEmployee()
    {
        return $this->hasOne(User::class, ['user_uuid' => 'employee_uuid']);
    }

    public function getMentor()
    {
        return $this->hasOne(User::class, ['user_uuid' => 'mentor_uuid']);
    }
}
