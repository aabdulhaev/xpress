<?php

namespace common\models\queries;

use common\models\ClientCoach;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\ClientCoach]].
 *
 * @see \common\models\ClientCoach
 */
class ClientCoachQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientCoach[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientCoach|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
