<?php

namespace common\models\queries;

use common\models\UserCompetence;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\UserCompetence]].
 *
 * @see \common\models\UserCompetence
 */
class UserCompetenceQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserCompetence[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserCompetence|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
