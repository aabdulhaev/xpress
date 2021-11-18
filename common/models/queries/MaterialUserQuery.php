<?php

namespace common\models\queries;

use common\models\MaterialUser;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\MaterialUser]].
 *
 * @see MaterialUser
 */
class MaterialUserQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return MaterialUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return MaterialUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
