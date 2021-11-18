<?php

namespace common\models\queries;

use common\models\EmployeeMentor;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\EmployeeMentor]].
 *
 * @see \common\models\EmployeeMentor
 */
class EmployeeMentorQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmployeeMentor[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmployeeMentor|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
