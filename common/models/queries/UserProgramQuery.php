<?php

namespace common\models\queries;

use common\models\UserProgram;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\UserProgram]].
 *
 * @see \common\models\UserProgram
 */
class UserProgramQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserProgram[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserProgram|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
