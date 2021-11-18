<?php

namespace common\repositories;

use common\access\Rbac;
use common\dispatchers\EventDispatcher;
use common\models\Program;
use common\models\User;
use RuntimeException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class UserRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function findByEmail($value): ?ActiveRecord
    {
        return User::find()->andWhere([['email' => $value]])->one();
    }

    public function get($id): ActiveRecord
    {
        return $this->getBy(['user_uuid' => $id]);
    }

    public function getByVerificationToken($token): ActiveRecord
    {
        return $this->getBy(['verification_token' => $token]);
    }

    public function getByEmail($email): ActiveRecord
    {
        return $this->getBy(['~*', User::tableName() . '.[[email]]', $email]);
    }

    public function getByPasswordResetToken($token): ActiveRecord
    {
        return $this->getBy(['password_reset_token' => $token]);
    }

    public function existsByPasswordResetToken(string $token): bool
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    public function save(User $user): void
    {
        if (!$user->save()) {
            Yii::error($user->errors);
            throw new RuntimeException('Ошибка сохранения пользователя');
        }
        Yii::info($user->attributes);
        $this->dispatch($user);
    }

    public function dispatch(User $user): void
    {
        $this->dispatcher->dispatchAll($user->releaseEvents());
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function remove(User $user): void
    {
        if (!$user->delete()) {
            throw new RuntimeException('Ошибка удаления пользователя');
        }
        $this->dispatch($user);
    }

    /**
     * @param array $uuids
     * @return ActiveQuery
     */
    public function getByUuid(array $uuids): ActiveQuery
    {
        return User::find()->andWhere(['IN', 'user_uuid', $uuids]);
    }

    public function getByRole($role): ActiveQuery
    {
        return User::find()->andWhere(['role' => $role]);
    }

    public function getEmployeeByProgram(User $user, string $program): Query
    {
        return User::find()
            ->alias('u')
            ->joinWith(['programAssignments up'])
            ->andWhere(['=', 'u.client_uuid', $user->client_uuid])
            ->andWhere(['=', 'u.role', Rbac::ROLE_EMP])
            ->andWhere(['=', 'up.program_uuid', Program::UuidByRole()[$program]]);
    }

    public function active(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(['status' => User::STATUS_ACTIVE]);
    }

    private function getBy(array $condition): ActiveRecord
    {
        $user = User::find()->andWhere($condition)->limit(1)->one();
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return $user;
    }

    public function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => ['user_uuid' => SORT_DESC],
                    'attributes' => [
                        'user_uuid' => [
                            'asc' => ['user_uuid' => SORT_ASC],
                            'desc' => ['user_uuid' => SORT_DESC],
                        ]
                    ],
                ],
                'pagination' => [
                    'pageSize' => false,
                    'pageSizeLimit' => [1, 100],
                ]
            ]
        );
    }
}
