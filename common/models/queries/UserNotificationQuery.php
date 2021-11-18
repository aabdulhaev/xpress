<?php

namespace common\models\queries;

use common\models\UserNotification;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\UserNotification]].
 *
 * @see \common\models\UserNotification
 */
class UserNotificationQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserNotification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserNotification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
