<?php

namespace api\modules\v1\controllers\actions\user;

use api\modules\v1\controllers\HelperTrait;
use common\forms\UserCreateForm;
use common\useCases\SignupCase;
use DomainException;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

/**
 * @OA\Post(
 *     path="/user/create",
 *     tags={"User"},
 *     summary="Создать пользователя",
 *     @OA\RequestBody(
 *         description="Данные для создания пользователя",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(ref="#/components/schemas/UserCreateForm")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Информация о созданном пользователе",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     )
 * )
 * */
class CreateAction extends \yii\rest\CreateAction
{
    use HelperTrait;

    private $useCase;

    public function __construct($id, $controller, SignupCase $useCase, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->useCase = $useCase;
    }

    public function run()
    {
        $form = new UserCreateForm();

        if ($this->validateBody($form)) {
            try {
                $form->user = $this->useCase->signup($form);

                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
                $response->getHeaders()->set(
                    'Location',
                    Url::toRoute(['user/view', 'id' => $form->user->user_uuid], true)
                );
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }
}
