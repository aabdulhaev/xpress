<?php

namespace common\models\queries;

use common\models\MaterialSubject;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\MaterialSubject]].
 *
 * @see MaterialSubject
 */
class MaterialSubjectQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return MaterialSubject[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return MaterialSubject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
