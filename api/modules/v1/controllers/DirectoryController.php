<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\forms\DirectoryForm;
use common\forms\DirectorySearchForm;
use common\repositories\DirectoryRepository;
use DomainException;
use Yii;
use yii\data\ActiveDataFilter;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\rest\IndexAction;
use yii\web\BadRequestHttpException;

class DirectoryController extends Controller
{
    use HelperTrait;

    public $repo;

    public function __construct($id, $module, DirectoryRepository $repo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repo = $repo;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [Rbac::ROLE_ADMIN, Rbac::ROLE_MODERATOR],
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        return [
            'index' => ['GET', 'HEAD', 'OPTIONS'],
            'view' => ['GET', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['PATCH', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS'],
        ];
    }

    /**
     * @OA\Get(
     *     path="/directory/{directory}",
     *     tags={"Directory"},
     *     summary="Возвращает элементы справочника <directory>",
     *     @OA\Parameter (
     *           name="directory",
     *           in="path",
     *           required=true,
     *           @OA\Schema(enum={"theme","tag", "language"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список элементов справочника",
     *     ),
     * )
     */
    public function actionIndex($directory)
    {
        $this->repo->checkDirectory($directory);

        $object = Yii::createObject([
            'class' => IndexAction::class,
            'modelClass' => $this->repo->modelClass,
            'dataFilter' => [
                'class' => ActiveDataFilter::class,
                'searchModel' => DirectorySearchForm::class,
                'queryOperatorMap' => [
                    'LIKE' => 'ILIKE',
                ]
            ],
            'prepareSearchQuery' => function (ActiveQuery $query, $requestParams) {
                return $query->andWhere(['=', 'status', $this->repo->modelClass::STATUS_ACTIVE]);
            },
        ]);

        return $object->run();
    }

    /**
     * @OA\Post (
     *     path="/directory/create/{directory}",
     *     tags={"Directory"},
     *     summary="Добавление элементов справочника <directory>",
     *     @OA\Parameter (
     *           name="directory",
     *           in="path",
     *           required=true,
     *           @OA\Schema(enum={"theme","tag", "language"})
     *     ),
     *     @OA\RequestBody(
     *         description="Данные для создании элементов",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/DirectoryForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация добавленного элемента справочника",
     *     ),
     * )
     */
    public function actionCreate($directory)
    {
        $this->repo->checkDirectory($directory);

        $form = new DirectoryForm();
        $form->modelClass = new $this->repo->modelClass;

        if ($this->validateBody($form)) {
            try {
                $model = $this->repo->create($form);
                Yii::$app->response->setStatusCode(201);
                $model->refresh();
                return $model;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Get(
     *     path="/directory/view/{directory}/{uuid}",
     *     tags={"Directory"},
     *     summary="Просмотр элемента <uuid> справочника <directory>",
     *     @OA\Parameter (
     *           name="directory",
     *           in="path",
     *           required=true,
     *           @OA\Schema(enum={"theme","tag", "language"})
     *     ),
     *     @OA\Parameter (
     *           name="uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация по элементу справочника",
     *     ),
     * )
     */
    public function actionView($directory, $uuid)
    {
        $this->repo->checkDirectory($directory);

        return $this->repo->get($uuid);
    }

    /**
     * @OA\Patch(
     *     path="/directory/update/{directory}/{uuid}",
     *     tags={"Directory"},
     *     summary="Редактирование элемента <uuid> справочника <directory>",
     *     @OA\Parameter (
     *           name="directory",
     *           in="path",
     *           required=true,
     *           @OA\Schema(enum={"theme","tag", "language"})
     *     ),
     *     @OA\Parameter (
     *           name="uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\RequestBody(
     *         description="Данные для редактирования элемента",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/DirectoryForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация отредактированного элемента справочника",
     *     ),
     * )
     */
    public function actionUpdate($directory, $uuid)
    {
        $this->repo->checkDirectory($directory);
        $model = $this->repo->get($uuid);

        $form = new DirectoryForm();
        $form->modelClass = $model;
        $form->load($model->toArray(), '');

        if ($this->validateBody($form)) {
            try {
                $this->repo->update($model, $form);
                $model->refresh();
                return $model;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Delete(
     *     path="/directory/delete/{directory}/{uuid}",
     *     tags={"Directory"},
     *     summary="Удаление элемента <uuid> справочника <directory>",
     *     @OA\Parameter (
     *           name="directory",
     *           in="path",
     *           required=true,
     *           @OA\Schema(enum={"theme","tag", "language"})
     *     ),
     *     @OA\Parameter (
     *           name="uuid",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content",
     *     ),
     * )
     */
    public function actionDelete($directory, $uuid)
    {
        $this->repo->checkDirectory($directory);
        $model = $this->repo->get($uuid);

        $this->repo->remove($model);

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
