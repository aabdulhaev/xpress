<?php

namespace common\models\queries;

use common\models\Program;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Program]].
 *
 * @see \common\models\Program
 */
class ProgramQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Program[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Program|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
