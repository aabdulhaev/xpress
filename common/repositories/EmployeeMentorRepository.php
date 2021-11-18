<?php

namespace common\repositories;


use common\access\Rbac;
use common\dispatchers\EventDispatcher;
use common\models\EmployeeMentor;
use common\models\User;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;

class EmployeeMentorRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get($employee_id, $mentor_id): EmployeeMentor
    {
        return $this->getBy(['employee_uuid' => $employee_id, 'mentor_uuid' => $mentor_id]);
    }

    public function save(EmployeeMentor $model): void
    {
        if (!$model->save()) {
            throw new \RuntimeException('Ошибка сохранения связи сотрудник - ментор');
        }
        $this->dispatcher->dispatchAll($model->releaseEvents());
    }

    public function remove(EmployeeMentor $model): void
    {
        if (!$model->delete()) {
            throw new \RuntimeException('Ошибка удаления связи сотрудник - ментор');
        }
        $this->dispatcher->dispatchAll($model->releaseEvents());
    }

    private function getBy(array $condition): EmployeeMentor
    {
        if (!$relation = EmployeeMentor::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Связь не найдена');
        }
        return $relation;
    }

    public function isConnectedMentor(User $employee, $mentor_uuid): bool
    {
        return $employee->getMentorAssignments()
            ->andWhere([
               'mentor_uuid' => $mentor_uuid,
               'status' => EmployeeMentor::STATUS_APPROVED
            ])
            ->exists();
    }

    public function isConnectedEmployee(User $mentor, $employee_uuid): bool
    {
        return $mentor->getEmployeeAssignments()
            ->andWhere([
               'employee_uuid' => $employee_uuid,
               'status' => EmployeeMentor::STATUS_APPROVED
           ])
            ->exists();
    }

    /**
     * @param string $mentor_uuid
     * @param string $client_uuid
     * @param int $status
     * @return array
     */
    public function getByMentorAndClientUuid(string $mentor_uuid, string $client_uuid, int $status): array
    {
        return (new ActiveQuery(EmployeeMentor::class))->select('*')
            ->from(['em' => EmployeeMentor::tableName()])
            ->where([
                'AND',
                ['em.status' => $status],
                ['em.mentor_uuid' => $mentor_uuid],
                [
                    'IN',
                    'em.employee_uuid',
                    (new Query())->select(['u.user_uuid'])
                        ->from(['u' => User::tableName()])
                        ->where([
                            'and',
                            ['u.client_uuid' => $client_uuid],
                            ['u.role' => Rbac::ROLE_EMP]
                        ])
                ]
            ])->all();
    }

    private function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => ['created_at' => SORT_DESC],
                ],
                'pagination' => [
                    'pageSizeLimit' => [5, 100],
                ]
            ]
        );
    }
}
