<?php

namespace common\models\queries;

use common\models\Section;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Section]].
 *
 * @see Section
 */
class SectionQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Section[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Section|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
