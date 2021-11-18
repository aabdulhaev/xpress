<?php

namespace api\modules\v1\controllers\actions\client;

use common\repositories\ClientRepository;
use yii\web\NotFoundHttpException;

class ViewAction extends \yii\rest\ViewAction
{
    public $clientRepo;

    public function __construct($id, $controller, ClientRepository $clientRepo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->clientRepo = $clientRepo;
    }

    public function run($id)
    {
        if (!$client = $this->clientRepo->find($id)) {
            throw new NotFoundHttpException("Клиент {$id} не найден.");
        }

        return $client;
    }
}
