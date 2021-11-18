<?php

namespace api\modules\v1\controllers\actions\material;

use api\modules\v1\controllers\HelperTrait;
use common\forms\material\MaterialCreateForm;
use common\repositories\MaterialRepository;
use DomainException;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * @OA\Post(
 *     path="/material/create",
 *     tags={"Material"},
 *     summary="Создание материала",
 *     @OA\RequestBody(
 *         description="Данные для создания материала",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(ref="#/components/schemas/MaterialCreateForm")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Информация о созданном материале",
 *         @OA\JsonContent(ref="#/components/schemas/Material"),
 *     )
 * )
 * */
class CreateAction extends \yii\rest\CreateAction
{
    use HelperTrait;

    private $repo;

    public function __construct($id, $controller, MaterialRepository $repo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->repo = $repo;
    }

    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $form = new MaterialCreateForm();

        if ($this->validateBody($form)) {
            try {
                $model = $this->repo->create($form);
                \Yii::$app->response->setStatusCode(201);
                $model->refresh();
                return $model;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }
}
