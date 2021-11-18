<?php

namespace api\modules\v1\controllers\actions\client;

use api\modules\v1\controllers\HelperTrait;
use common\forms\ClientForm;
use common\useCases\ClientCase;
use DomainException;
use Yii;
use yii\web\BadRequestHttpException;

class UpdateAction extends \yii\rest\UpdateAction
{
    use HelperTrait;

    public $clientCase;

    public function __construct($id, $controller, ClientCase $clientCase, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->clientCase = $clientCase;
    }


    /**
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function run($id)
    {
        $form = new ClientForm();

        if ($this->validateBody($form)) {
            try {
                $client = $this->clientCase->edit($id, $form);
                Yii::$app->response->setStatusCode(201);
                $client->refresh();

                return $client;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }
}
