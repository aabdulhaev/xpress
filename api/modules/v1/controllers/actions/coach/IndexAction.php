<?php

namespace api\modules\v1\controllers\actions\coach;

use common\models\User;
use common\repositories\UserRepository;
use yii\data\ActiveDataProvider;

class IndexAction extends \yii\rest\IndexAction
{
    private $userRepo;

    public function __construct($id, $controller, UserRepository $userRepo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->userRepo = $userRepo;
    }

    /**
     * Список всех коучей добавленных в компанию текущего пользователя (hr)
     *
     * @return ActiveDataProvider
     */
    public function run(): ActiveDataProvider
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;
        $query = $user->getClientCoaches();
        return $this->userRepo->getProvider($query);
    }
}
