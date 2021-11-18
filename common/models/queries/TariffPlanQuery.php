<?php

namespace common\models\queries;

use common\models\TariffPlan;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\TariffPlan]].
 *
 * @see \common\models\TariffPlan
 */
class TariffPlanQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TariffPlan[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TariffPlan|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
