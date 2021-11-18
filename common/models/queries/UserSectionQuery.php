<?php

namespace common\models\queries;

use common\models\UserSection;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\UserSection]].
 *
 * @see UserSection
 */
class UserSectionQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return UserSection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserSection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
