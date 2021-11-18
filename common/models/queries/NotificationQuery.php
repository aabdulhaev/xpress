<?php

namespace common\models\queries;

use common\models\Notification;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Notification]].
 *
 * @see \common\models\Notification
 */
class NotificationQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Notification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Notification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
