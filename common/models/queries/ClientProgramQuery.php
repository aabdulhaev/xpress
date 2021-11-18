<?php

namespace common\models\queries;

use common\models\ClientProgram;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\ClientProgram]].
 *
 * @see \common\models\ClientCoach
 */
class ClientProgramQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientProgram[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientProgram|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
