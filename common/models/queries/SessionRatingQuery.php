<?php

namespace common\models\queries;

use common\models\SessionRating;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\SessionRating]].
 *
 * @see \common\models\SessionRating
 */
class SessionRatingQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SessionRating[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SessionRating|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
