<?php

namespace common\repositories;

use common\forms\training\TrainingSearchForm;
use common\access\Rbac;
use common\dispatchers\EventDispatcher;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\db\QueryInterface;

class TrainingRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get($id): TrainingSession
    {
        return $this->getBy(['training_uuid' => $id]);
    }

    public function save(TrainingSession $training): void
    {
        if (!$training->save()) {
            throw new \RuntimeException('Ошибка сохранения тренинга');
        }
        $this->dispatcher->dispatchAll($training->releaseEvents());
    }

    public function dispatch(TrainingSession $training): void
    {
        $this->dispatcher ? $this->dispatcher->dispatchAll($training->releaseEvents()) : '';
    }

    public function remove(TrainingSession $training): void
    {
        if (!$training->delete()) {
            throw new \RuntimeException('Ошибка удаления тренинга');
        }
        $this->dispatcher->dispatchAll($training->releaseEvents());
    }

    public function getByUuid(array $uuids): ActiveQuery
    {
        return TrainingSession::find()->andWhere(['IN', 'training_uuid', $uuids]);
    }

    public function active(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(['status' => TrainingSession::STATUS_CONFIRM]);
    }

    private function getBy(array $condition): TrainingSession
    {
        $training = TrainingSession::find()->andWhere($condition)->limit(1)->one();
        if (!$training) {
            throw new NotFoundException('Тренинг не найден');
        }
        return $training;
    }

    public function search(TrainingSearchForm $form): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;

        $query = $user->getNotDeletedTrainings();

        if (!empty($form->scenario)) {
            switch ($form->scenario) {
                case 'confirmed':
                    $query = $user->getConfirmedTrainings();
                    break;
                case 'for-rating':
                    $query = $user->getTrainingsNeedSelfRating();
                    break;
                case 'for-complete':
                    $query = $user->getCompletedTrainings()
                        ->andWhere([
                            '<',
                            'start_at_tc',
                            date_create()->format('Y-m-d H:i:sP')
                        ]);
                    break;
                case 'free':
                    if ($user->role === Rbac::ROLE_EMP) {
                        $query = $user->getMyCoachesFreeTraining();
                    } else {
                        $query = $user->getTrainings()
                            ->andWhere(['status' => TrainingSession::STATUS_FREE]);
                    }
                    break;
                case 'wait-confirm':
                    $query = $user->getTrainingsWaitConfirm();

                    break;
                case 'need-confirm':
                    $query = $user->getTrainingsNeedSelfConfirm();

                    break;
                default:
                    break;
            }
        } else {
            if ($user->role === Rbac::ROLE_HR) {
                $query = $this->getHrTrainings($form, $user->client_uuid);
            }
        }

        $query = !empty($form->start_at_from) ? $query->andWhere(['>=', TrainingSession::tableName() .'.start_at_tc', $form->start_at_from]) : $query;
        $query = !empty($form->start_at_to) ? $query->andWhere(['<=', TrainingSession::tableName() . '.start_at_tc', $form->start_at_to]) : $query;
        $query = !empty($form->status) ? $query->andFilterWhere([TrainingSession::tableName() . '.status' => $form->status]) : $query;

        return $this->getProvider($query);
    }

    /**
     * Получаем все сессии для hr
     *
     * @param TrainingSearchForm $form
     * @param string $clientUuid
     * @return ActiveQuery
     */
    public function getHrTrainings(TrainingSearchForm $form, string $clientUuid): ActiveQuery
    {
        return TrainingSession::find()
            ->joinWith('userAssignments.user')
            ->andWhere(['>', TrainingSession::tableName() . '.start_at_tc', $form->start_at_from])
            ->andWhere(['<', TrainingSession::tableName() . '.start_at_tc', $form->start_at_to])
            ->andWhere(['IN', TrainingSession::tableName() . '.status', [
                TrainingSession::STATUS_CONFIRM,
                TrainingSession::STATUS_COMPLETED,
                TrainingSession::STATUS_RATED,
                TrainingSession::STATUS_CANCEL
            ]])
            ->andWhere([User::tableName() . '.client_uuid' => $clientUuid]);
    }

    private function getProvider(QueryInterface $query): ActiveDataProvider
    {
        return new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => ['start_at_tc' => SORT_DESC],
                    'attributes' => [
                        'training_uuid' => [
                            'asc' => ['training_uuid' => SORT_ASC],
                            'desc' => ['training_uuid' => SORT_DESC],
                        ],
                        'start_at_tc' => [
                            'asc' => ['start_at_tc' => SORT_ASC],
                            'desc' => ['start_at_tc' => SORT_DESC],
                        ]
                    ],
                ],
                'pagination' => [
                    'pageSizeLimit' => [1, 1000],
                ]
            ]
        );
    }

    public function getByMentorAndEmployees(string $coach_uuid, array $employees)
    {
        return (new ActiveQuery(UserTraining::class))->select('*')
            ->from(['ut' => UserTraining::tableName()])
            ->where(
                [
                    'AND',
                    [
                        'IN',
                        'ut.training_uuid',
                        (new Query())->select('ts.training_uuid')
                            ->from(['ts' => TrainingSession::tableName()])
                            ->where(
                                [
                                    'AND',
                                    ['!=', 'ts.status', TrainingSession::STATUS_DELETED],
                                    'ts.start_at_tc > current_timestamp',
                                    [
                                        'IN',
                                        'ts.training_uuid',
                                        (new Query())->select('ut1.training_uuid')
                                            ->from(['ut1' => UserTraining::tableName()])
                                            ->where([
                                                'AND',
                                                [
                                                    'IN',
                                                    'ut1.status',
                                                    [UserTraining::STATUS_NOT_CONFIRM, UserTraining::STATUS_CONFIRM]
                                                ]
                                            ])
                                            ->andWhere([
                                                'OR',
                                                ['ut1.user_uuid' => $coach_uuid],
                                                ['IN', 'ut1.user_uuid', $employees]
                                            ])
                                            ->groupBy('ut1.training_uuid')
                                            ->having('COUNT(ut1.user_uuid) = 2')
                                    ]
                                ]
                            )
                            ->groupBy('ts.training_uuid')
                    ]
                ]
            )->all();
    }
}
