<?php


namespace common\models\queries;


use common\models\Competence;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Competence]].
 *
 * @see \common\models\Competence
 */
class CompetenceQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Competence[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Competence|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
