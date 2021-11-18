<?php

namespace common\models\queries;

use common\models\Client;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Client]].
 *
 * @see \common\models\Client
 */
class ClientQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['status' => Client::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     * @return Client[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Client|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
