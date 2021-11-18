<?php

namespace common\models\queries;

use common\models\Tag;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Tag]].
 *
 * @see Tag
 */
class TagQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Tag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Tag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
