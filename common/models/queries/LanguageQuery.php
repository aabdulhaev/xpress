<?php

namespace common\models\queries;

use common\models\Language;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Language]].
 *
 * @see Language
 */
class LanguageQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Language[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Language|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
