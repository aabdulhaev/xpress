<?php

namespace common\models\queries;

use common\models\ClientTariff;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\ClientTariff]].
 *
 * @see \common\models\ClientTariff
 */
class ClientTariffQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientTariff[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientTariff|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
