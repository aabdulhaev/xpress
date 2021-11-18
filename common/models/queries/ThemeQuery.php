<?php

namespace common\models\queries;

use common\models\Theme;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Theme]].
 *
 * @see Theme
 */
class ThemeQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Theme[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Theme|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
