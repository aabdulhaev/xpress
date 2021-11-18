<?php

namespace common\models\queries;

use common\models\TrainingSession;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\TrainingSession]].
 *
 * @see \common\models\TrainingSession
 */
class TrainingSessionQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TrainingSession[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TrainingSession|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function scheduled() : ActiveQuery
    {
        return $this->andWhere(['status' => TrainingSession::STATUS_CONFIRM]);
    }

    public function notConfirm() : ActiveQuery
    {
        return $this->andWhere(['status' => TrainingSession::STATUS_NOT_CONFIRM]);
    }

    public function free() : ActiveQuery
    {
        return $this->andWhere(['status' => TrainingSession::STATUS_FREE]);
    }

}
