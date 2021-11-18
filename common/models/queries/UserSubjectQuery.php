<?php

namespace common\models\queries;

use common\models\UserSubject;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\UserSubject]].
 *
 * @see \common\models\UserSubject
 */
class UserSubjectQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserSubject[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserSubject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
