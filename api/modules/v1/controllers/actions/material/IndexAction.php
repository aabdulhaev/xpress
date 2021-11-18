<?php

namespace api\modules\v1\controllers\actions\material;

use api\modules\v1\controllers\HelperTrait;
use common\forms\material\MaterialSearchForm;
use common\repositories\MaterialRepository;
use yii\data\ActiveDataProvider;

/**
 * @OA\Get(
 *     path="/material/index",
 *     tags={"Material"},
 *     summary="Возвращает список материалов.",
 *     @OA\Response(
 *         response=200,
 *         description="Список материалов",
 *         @OA\JsonContent(ref="#/components/schemas/Material")),
 *     ),
 * )
 */
class IndexAction extends \yii\rest\IndexAction
{
    use HelperTrait;

    private $repo;

    public function __construct($id, $controller, MaterialRepository $repo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->repo = $repo;
    }

    /**
     * Получение всех материалов
     *
     * @return MaterialSearchForm|ActiveDataProvider
     */
    public function run()
    {
        $form = new MaterialSearchForm();
        if ($this->validateQuery($form)) {
            return $this->repo->search($form);
        }
        return $form;
    }
}
