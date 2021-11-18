<?php

namespace common\models\queries;

use common\models\UserTraining;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\UserTraining]].
 *
 * @see \common\models\UserTraining
 */
class UserTrainingQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserTraining[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserTraining|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
