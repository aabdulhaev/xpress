<?php

namespace common\models\queries;

use common\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Request]].
 *
 * @see \common\models\Request
 */
class RequestQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Request[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Request|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
