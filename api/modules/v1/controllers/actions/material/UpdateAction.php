<?php

namespace api\modules\v1\controllers\actions\material;

use api\modules\v1\controllers\HelperTrait;
use common\forms\material\MaterialUpdateForm;
use common\repositories\MaterialRepository;
use DomainException;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * @OA\Patch(
 *     path="/material/update/{id}",
 *     tags={"Material"},
 *     summary="Редактирование материала",
 *     @OA\Parameter (
 *           name="id",
 *           in="path",
 *           required=true,
 *     ),
 *     @OA\RequestBody(
 *         description="Данные для редактирования материала",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(ref="#/components/schemas/MaterialUpdateForm")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description = "Информация об обновлённом материале",
 *         @OA\JsonContent(ref="#/components/schemas/Material"),
 *     )
 * )
 * */
class UpdateAction extends \yii\rest\UpdateAction
{
    use HelperTrait;

    private $repo;

    public function __construct($id, $controller, MaterialRepository $repo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->repo = $repo;
    }

    public function run($id)
    {
        $material = $this->repo->get($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $material);
        }

        $form = new MaterialUpdateForm($material);

        if ($this->validateBody($form)) {
            try {
                $this->repo->update($material, $form);
                $material->refresh();
                return $material;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }
}
