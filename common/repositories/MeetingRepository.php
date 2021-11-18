<?php

declare(strict_types=1);

namespace common\repositories;

use common\access\Rbac;
use common\dispatchers\EventDispatcher;
use common\forms\meeting\MeetingSearchForm;
use common\models\Meeting;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\QueryInterface;
use yii\web\NotFoundHttpException;

class MeetingRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get($id): Meeting
    {
        if (!$model = Meeting::findOne($id)) {
            throw new NotFoundException("Митинг {$id} не найден");
        }
        return $model;
    }

    public function save(Meeting $model): void
    {
        if (!$model->save()) {
            \Yii::error($model->errors);
            throw new \RuntimeException('Ошибка сохранения митинга.');
        }

        $this->dispatcher->dispatchAll($model->releaseEvents());
    }

    /**
     * @param $id
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function getByTraining($id): ActiveRecord
    {
        $meeting = Meeting::find()
            ->andWhere(['training_uuid' => $id])
            ->one();
        if (empty($meeting)) {
            throw new NotFoundHttpException('Вебинар не найден');
        }

        return $meeting;
    }

    public function search(MeetingSearchForm $form): ActiveDataProvider
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        $query = $user->getMeetings();

        if (in_array($user->role, [Rbac::ROLE_ADMIN, Rbac::ROLE_MODERATOR])) {
            $query = Meeting::find()
                ->alias('m')
                ->andWhere(['=', 'm.type', Meeting::TYPE_GROUP_MEETING])
                ->andWhere(['!=', 'm.status', Meeting::STATUS_DELETED]);
        }


        $query = !empty($form->start_at) ? $query->andWhere(['>=', 'm.start_at', $form->start_at]) : $query;
        $query = !empty($form->end_at) ? $query->andWhere(['<=', 'm.start_at', $form->end_at]) : $query;

        return $this->getProvider($query);
    }

    private function getProvider(QueryInterface $query): ActiveDataProvider
    {
        return new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => ['start_at' => SORT_DESC],
                    'attributes' => [
                        'start_at' => [
                            'asc' => ['start_at' => SORT_ASC],
                            'desc' => ['start_at' => SORT_DESC],
                        ]
                    ],
                ],
                'pagination' => [
                    'pageSizeLimit' => [1, 1000],
                ]
            ]
        );
    }
}
