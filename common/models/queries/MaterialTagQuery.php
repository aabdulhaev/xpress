<?php

namespace common\models\queries;

use common\models\MaterialTag;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\MaterialTag]].
 *
 * @see MaterialTag
 */
class MaterialTagQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return MaterialTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return MaterialTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
