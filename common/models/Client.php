<?php

namespace common\models;

use common\models\queries\ClientQuery;
use common\useCases\ClientCase;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Ramsey\Uuid\Uuid;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "client".
 *
 * @property string $client_uuid
 * @property string $name
 * @property int $status
 * @property string $logo
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 *
 * @property ClientTariff[] $tariffAssignments
 * @property TariffPlan[] $tariffs
 * @property ClientProgram[] $programAssignments
 * @property Program[] $programs
 * @property ClientCoach[] $coachAssignments
 * @property User[] $approvedCoaches
 * @property User[] $coaches
 */
class Client extends ActiveRecord
{

    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%client}}';
    }

    /**
     * {@inheritdoc}
     * @return ClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientQuery(get_called_class());
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['tariffAssignments', 'programAssignments', 'coachAssignments'],
            ],
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_NOT_ACTIVE
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
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'client_uuid' => 'Client Uuid',
            'name' => 'Name',
            'status' => 'Status',
            'logo' => 'Logo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public function fields()
    {
        return [
          'client_uuid',
          'name',
          'logo',
          'status'
        ];
    }

    public function extraFields()
    {
        return [
            'tariff',
            'programs' => function (self $model) {
                return $model->approvedPrograms;
            },
            'coaches' => function (self $model) {
                return $model->approvedCoaches;
            }
        ];
    }

    /**** RELATIONS ****/
    public function getUsers()
    {
        return $this->hasMany(User::class, ['client_uuid' => 'client_uuid']);
    }

    public function getCoachAssignments(): ActiveQuery
    {
        return $this->hasMany(ClientCoach::class, ['client_uuid' => 'client_uuid']);
    }

    public function getCoaches(): ActiveQuery
    {
        return $this->hasMany(User::class, ['user_uuid' => 'coach_uuid'])
            ->via('coachAssignments');
    }

    public function getApprovedCoaches(): ActiveQuery
    {
        return $this->hasMany(User::class, ['user_uuid' => 'coach_uuid'])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->via('coachAssignments', function (ActiveQuery $query) {
                return $query->andOnCondition([ClientCoach::tableName() . '.status' => ClientCoach::STATUS_APPROVED]);
            });
    }

    public function getTariffAssignments(): ActiveQuery
    {
        return $this->hasMany(ClientTariff::class, ['client_uuid' => 'client_uuid']);
    }

    public function getTariffs(): ActiveQuery
    {
        return $this->hasMany(TariffPlan::class, ['tariff_uuid' => 'tariff_uuid'])
            ->via('tariffAssignments');
    }

    public function getTariff(): ActiveQuery
    {
        return $this->hasOne(ClientTariff::class, ['client_uuid' => 'client_uuid'])
            ->andWhere(['status' => ClientTariff::STATUS_ACTIVE]);
    }

    public function getProgramAssignments(): ActiveQuery
    {
        return $this->hasMany(ClientProgram::class, ['client_uuid' => 'client_uuid']);
    }

    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(Program::class, ['program_uuid' => 'program_uuid'])
            ->via('programAssignments');
    }

    public function getApprovedPrograms(): ActiveQuery
    {
        return $this->hasMany(Program::class, ['program_uuid' => 'program_uuid'])
            ->via('programAssignments', function (ActiveQuery $query) {
                return $query->andOnCondition([
                    ClientProgram::tableName() . '.status' => ClientProgram::STATUS_APPROVED
                ]);
            });
    }

    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(ClientProgram::class, ['client_uuid' => 'client_uuid'])
            ->andWhere(['status' => ClientProgram::STATUS_APPROVED]);
    }

    /**** OPERATIONS ***
     * @param $name
     * @return Client
     */
    public static function create($name): self
    {
        $model = new static();
        $model->status = self::STATUS_ACTIVE;
        $model->client_uuid = Uuid::uuid6();
        $model->name = $name;

        return $model;
    }

    public function edit($name): void
    {
        $this->name = $name;
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function assignCoach($coach_id): void
    {
        $assignments = $this->coachAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->isForCoach($coach_id)) {
                $new = false;

                if (!$assignment->isApproved()) {
                    $assignment->status = ClientCoach::STATUS_APPROVED;
                    $assignment->save();
                }
            }
        }

        if ($new) {
            $assignments[] = ClientCoach::create($this->client_uuid, $coach_id);
        }

        $this->coachAssignments = $assignments;
    }

    public function assignCoaches($coaches)
    {
        $clientCoachUuids = array_column($this->coachAssignments, 'coach_uuid');
        $draftCoaches = array_diff($clientCoachUuids, $coaches);

        foreach ($coaches as $coach) {
            $this->assignCoach($coach);
        }
        foreach ($draftCoaches as $draftCoach) {
            $this->revokeCoach($draftCoach);
        }
    }

    public function revokeCoach($coach_id): void
    {
        $coachAssignments = $this->coachAssignments;
        foreach ($coachAssignments as $assignment) {
            if ($assignment->isForCoach($coach_id) && !$assignment->isDraft()) {
                $assignment->status = ClientCoach::STATUS_DRAFT;
            }
        }
        $this->coachAssignments = $coachAssignments;
    }

    public function revokeCoaches(): void
    {
        $this->coachAssignments = [];
    }

    public function assignTariff($tariff_id, $expire_at): void
    {
        $assignments = $this->tariffAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->isForTariff($tariff_id)) {
                $new = false;
                if (!$assignment->isActive()) {
                    $assignment->status = ClientTariff::STATUS_ACTIVE;
                }
            } else {
                if ($assignment->isActive()) {
                    $assignment->status = ClientTariff::STATUS_CANCEL;
                }
            }
        }
        if ($new) {
            $assignments[] = ClientTariff::create($tariff_id, $expire_at);
        }
        $this->tariffAssignments = $assignments;
    }

    public function revokeTariff($tariff_id): void
    {
        $assignments = $this->tariffAssignments;
        foreach ($assignments as $i => $assignment) {
            if ($assignment->isForTariff($tariff_id)) {
                unset($assignments[$i]);
                $this->tariffAssignments = $assignments;
                return;
            }
        }
        throw new \DomainException('Тариф не найден.');
    }

    public function revokeTariffs(): void
    {
        $this->tariffAssignments = [];
    }

    public function assignProgram($program_id): void
    {
        $assignments = $this->programAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->isForProgram($program_id)) {
                $new = false;

                if (!$assignment->isApproved()) {
                    $assignment->status = ClientProgram::STATUS_APPROVED;
                    $assignment->save();
                }
            }
        }

        if ($new) {
            $assignments[] = ClientProgram::create($this->client_uuid, $program_id);
        }

        $this->programAssignments = $assignments;
    }

    public function revokeProgram($program_id): void
    {
        $assignments = $this->programAssignments;

        foreach ($assignments as $i => $assignment) {
            if ($assignment->isForProgram($program_id)) {
                if ($assignment->isApproved()) {
                    $assignment->status = ClientProgram::STATUS_DECLINE;
                    $assignment->save();
                }

                $this->programAssignments = $assignments;
                return;
            }
        }
        throw new \DomainException('Программа не найдена.');
    }

    public function revokePrograms(): void
    {
        $this->programAssignments = [];
    }
}
