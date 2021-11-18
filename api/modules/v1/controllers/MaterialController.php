<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\actions\material\{CreateAction, IndexAction, UpdateAction};
use common\access\Rbac;
use common\forms\material\{MaterialBindForm, MaterialSearchForm, MaterialUnbindForm};
use common\models\{Language, Material, MaterialUser, Section, Tag, Theme, User};
use common\repositories\MaterialRepository;
use RuntimeException;
use Yii;
use yii\data\ActiveDataFilter;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\DeleteAction;
use yii\rest\ViewAction;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * @OA\Get(
 *     path="/material/view/{id}",
 *     tags={"Material"},
 *     summary="Просмотр материала с идентификатором <id>",
 *     @OA\Parameter (
 *           name="id",
 *           in="path",
 *           required=true,
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description = "Информация о материале",
 *         @OA\JsonContent(ref="#/components/schemas/Material"),
 *     )
 * )
 *
 * @OA\Get(
 *     path="/material/moderating",
 *     tags={"Material"},
 *     summary="Возвращает список материалов для модерации",
 *     @OA\Response(
 *         response=200,
 *         description="Список метериалов",
 *         @OA\JsonContent(ref="#/components/schemas/Material")),
 *     ),
 * )
 *
 * @OA\Delete (
 *     path="/material/delete/{id}",
 *     tags={"Material"},
 *     summary="Удаление материала с идентификатором <id>",
 *     @OA\Parameter (
 *           name="id",
 *           in="path",
 *           required=true,
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No content",
 *     )
 * )
 */
class MaterialController extends ActiveController
{
    use HelperTrait;

    public $modelClass = Material::class;
    public $materialRepo;

    public function __construct($id, $module, MaterialRepository $materialRepo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->materialRepo = $materialRepo;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['all-themes', 'all-tags', 'all-languages',]
                ],
                [
                    'allow' => true,
                    'actions' => ['moderating', 'view', 'approve', 'decline', 'delete', 'bind', 'unbind'],
                    'roles' => [Rbac::ROLE_MODERATOR],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete', 'elected', 'bind', 'unbind'],
                    'roles' => [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'elected', 'learned',],
                    'roles' => [Rbac::ROLE_EMP],
                ],
            ],
        ];
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'OPTIONS'],
            'learned' => ['PATCH', 'OPTIONS'],
            'elected' => ['PATCH', 'OPTIONS'],
            'bind' => ['POST', 'OPTIONS'],
            'unbind' => ['POST', 'OPTIONS'],
            'view' => ['GET', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['PATCH', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS'],
            'moderating' => ['GET', 'OPTIONS'],
            'approve' => ['PATCH', 'OPTIONS'],
            'decline' => ['PATCH', 'OPTIONS'],
            'all-themes' => ['GET', 'OPTIONS'],
            'all-tags' => ['GET', 'OPTIONS'],
            'all-languages' => ['GET', 'OPTIONS'],
        ];
    }

    public function actions(): array
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'class' => IndexAction::class,
                    'modelClass' => $this->modelClass
                ],
                'view' => [
                    'class' => ViewAction::class,
                    'modelClass' => $this->modelClass
                ],
                'create' => [
                    'class' => CreateAction::class,
                    'modelClass' => $this->modelClass
                ],
                'update' => [
                    'class' => UpdateAction::class,
                    'modelClass' => $this->modelClass
                ],
                'delete' => [
                    'class' => DeleteAction::class,
                    'modelClass' => $this->modelClass
                ],
                'moderating' => [
                    'class' => \yii\rest\IndexAction::class,
                    'modelClass' => $this->modelClass,
                    'dataFilter' => [
                        'class' => ActiveDataFilter::class,
                        'searchModel' => MaterialSearchForm::class,
                        'queryOperatorMap' => [
                            'LIKE' => 'ILIKE',
                        ],
                        'attributeMap' => [
                            'theme' => 'm.theme_uuid',
                            'tags' => 't.tag_uuid',
                            'subjects' => 's.subject_uuid',
                            'language' => 'm.language_uuid',
                            'status' => 'm.status',
                            'created_by' => 'm.created_by',
                            'updated_by' => 'm.updated_by',
                        ]
                    ],
                    'prepareSearchQuery' => function (ActiveQuery $query, $requestParams) {
                        return $query->alias('m')
                            ->distinct()
                            ->joinWith(['tags t', 'subjects s'])
                            ->andWhere(['!=', 'm.status', Material::STATUS_DELETED])
                            ->andWhere([
                                'or',
                                ['=', 'm.type', Material::TYPE_TASK],
                                ['and',
                                    ['=', 'm.type', Material::TYPE_LIBRARY],
                                    ['<', 'm.created_at', (time() - (60 * 60 * 12))]
                                ]
                            ]);
                    },
                    'checkAccess' => [$this, 'checkAccess']
                ],
            ]
        );
    }

    /**
     * @param $id
     * @return Material
     * @throws BadRequestHttpException
     *
     * @OA\Patch(
     *     path="/material/elected/{id}",
     *     tags={"Material"},
     *     summary="Отметка материала как избранное",
     *     @OA\Parameter (
     *           name="id",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Информация об обновлённом материале",
     *         @OA\JsonContent(ref="#/components/schemas/Material"),
     *     )
     * )
     */
    public function actionElected($id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $model = $this->materialRepo->get($id);

        $this->checkAccess($this->action->id, $model);

        /* @var $materialUser MaterialUser */
        if ($materialUser = $model->getMaterialUser($user)) {
            if ($materialUser->isAccessed || $model->type == Material::TYPE_LIBRARY || $model->created_by == $user->user_uuid) {
                $this->materialRepo->changeElected($materialUser);

                return $model;
            }
        } elseif ($model->type == Material::TYPE_LIBRARY || $model->created_by == $user->user_uuid) {
            $userAssignments = $model->userAssignments;
            $userAssignments[] = MaterialUser::create($model->material_uuid, $user->user_uuid, MaterialUser::NOT_ACCESSED, MaterialUser::ELECTED);
            $model->userAssignments = $userAssignments;

            $this->materialRepo->save($model);
            $model->refresh();
            return $model;
        }

        throw new BadRequestHttpException('Материал не доступен для пользователя');
    }

    /**
     * @param $id
     * @return Material
     * @throws BadRequestHttpException
     *
     * @OA\Patch(
     *     path="/material/learned/{id}",
     *     tags={"Material"},
     *     summary="Отметка материала как изучено",
     *     @OA\Parameter (
     *           name="id",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Информация об обновлённом материале",
     *         @OA\JsonContent(ref="#/components/schemas/Material"),
     *     )
     * )
     */
    public function actionLearned($id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $model = $this->materialRepo->get($id);

        $this->checkAccess($this->action->id, $model);

        /* @var $materialUser MaterialUser */
        if ($materialUser = $model->getMaterialUser($user)) {
            if ($materialUser->isAccessed || $model->type == Material::TYPE_LIBRARY) {
                $this->materialRepo->changeLearned($materialUser);

                return $model;
            }
        } elseif ($model->type == Material::TYPE_LIBRARY) {
            $userAssignments = $model->userAssignments;
            $userAssignments[] = MaterialUser::create($model->material_uuid, $user->user_uuid, MaterialUser::NOT_ACCESSED, MaterialUser::NOT_ELECTED, MaterialUser::LEARNED);
            $model->userAssignments = $userAssignments;

            $this->materialRepo->save($model);
            $model->refresh();
            return $model;
        }

        throw new BadRequestHttpException('Материал не доступен для пользователя');
    }

    /**
     * @param $id
     * @return MaterialBindForm
     * @throws RuntimeException
     *
     * @OA\Post(
     *     path="/material/bind/{id}",
     *     tags={"Material"},
     *     summary="Привязка материала с идентификатором <id> выбранным пользователям",
     *     @OA\Parameter (
     *           name="id",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\RequestBody(
     *         description="Список пользователей",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/MaterialBindForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Список пользователей",
     *         @OA\JsonContent(ref="#/components/schemas/MaterialBindForm"),
     *     )
     * )
     */
    public function actionBind($id)
    {
        $material = $this->materialRepo->get($id);

        $this->checkAccess($this->action->id, $material);

        $model = new MaterialBindForm();

        if ($this->validateBody($model)) {
            $this->materialRepo->bind($material, $model);
        }

        return $model;
    }

    /**
     * @param $id
     * @return MaterialUnbindForm
     * @throws RuntimeException
     *
     * @OA\Post(
     *     path="/material/unbind/{id}",
     *     tags={"Material"},
     *     summary="Отвязка материала с идентификатором <id> от выбранных пользователей",
     *     @OA\Parameter (
     *           name="id",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\RequestBody(
     *         description="Список пользователей",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/MaterialBindForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Список пользователей",
     *         @OA\JsonContent(ref="#/components/schemas/MaterialBindForm"),
     *     )
     * )
     */
    public function actionUnbind($id)
    {
        $material = $this->materialRepo->get($id);

        $this->checkAccess($this->action->id, $material);

        $model = new MaterialUnbindForm($material);

        if ($this->validateBody($model)) {
            $this->materialRepo->unbind($material, $model);
        }

        return $model;
    }

    /**
     * @param $id
     * @throws RuntimeException|ForbiddenHttpException
     *
     * @OA\Patch(
     *     path="/material/approve/{id}",
     *     tags={"Material"},
     *     summary="Одобрение материала с идентификатором <id> модератором",
     *     @OA\Parameter (
     *           name="id",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description = "No content",
     *     )
     * )
     */
    public function actionApprove($id)
    {
        $material = $this->materialRepo->get($id);

        $this->checkAccess($this->action->id, $material);

        /** @var User $user */
        $user = \Yii::$app->user->identity;

        $material->status = Material::STATUS_PUBLISHED;
        $material->approve_by = $user->user_uuid;
        $material->approve_at = time();

        $this->materialRepo->save($material);

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * @param $id
     * @throws RuntimeException|ForbiddenHttpException
     *
     * @OA\Patch(
     *     path="/material/decline/{id}",
     *     tags={"Material"},
     *     summary="Отклонение материала с идентификатором <id> модератором",
     *     @OA\Parameter (
     *           name="id",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description = "No content",
     *     )
     * )
     */
    public function actionDecline($id)
    {
        $material = $this->materialRepo->get($id);

        $this->checkAccess($this->action->id, $material);

        /** @var User $user */
        $user = \Yii::$app->user->identity;

        $material->status = Material::STATUS_DECLINED;
        $material->approve_by = $user->user_uuid;
        $material->approve_at = time();

        $this->materialRepo->save($material);

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * @return array|Theme[]
     *
     * @OA\Get(
     *     path="/material/all-themes",
     *     tags={"Material"},
     *     summary="Возвращает список тем",
     *     @OA\Response(
     *         response = 200,
     *         description = "Список всех тем",
     *         @OA\JsonContent(ref="#/components/schemas/Theme"),
     *     )
     * )
     */
    public function actionAllThemes(): array
    {
        return Theme::find()->where(['status' => Theme::STATUS_ACTIVE])->all();
    }

    /**
     * @return array|Tag[]
     *
     * @OA\Get(
     *     path="/material/all-tags",
     *     tags={"Material"},
     *     summary="Возвращает список тегов",
     *     @OA\Response(
     *         response = 200,
     *         description = "Список всех тегов",
     *         @OA\JsonContent(ref="#/components/schemas/Tag"),
     *     )
     * )
     */
    public function actionAllTags(): array
    {
        return Tag::find()->where(['status' => Tag::STATUS_ACTIVE])->all();
    }

    /**
     * @return array|Language[]
     *
     * @OA\Get(
     *     path="/material/all-languages",
     *     tags={"Material"},
     *     summary="Возвращает список языков",
     *     @OA\Response(
     *         response = 200,
     *         description = "Список всех языков",
     *         @OA\JsonContent(ref="#/components/schemas/Language"),
     *     )
     * )
     */
    public function actionAllLanguages(): array
    {
        return Language::find()->where(['status' => Language::STATUS_ACTIVE])->all();
    }

    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var Material $model */
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $sections = ArrayHelper::getColumn($user->sections, 'section_uuid');
        $forbidden = new ForbiddenHttpException('Вы не можете выполнять данное действие.');

        if (in_array($action, ['moderating', 'approve', 'decline'])) {
            if (!in_array(Section::SECTION_LIBRARY_UUID, $sections)) {
                throw $forbidden;
            }

            if (in_array($action, ['approve', 'decline'])) {
                if ($model->type == Material::TYPE_TASK) {
                    throw $forbidden;
                } elseif ($model->type == Material::TYPE_LIBRARY) {
                    if (time() < intval($model->created_at) + (60 * 60 * 12)) {
                        throw $forbidden;
                    }
                }
            }
        }

        if ($action == 'update') {
            if ($model->created_by == $user->user_uuid) {
                if ($model->type == Material::TYPE_LIBRARY) {
                    if ($model->status == Material::STATUS_NOT_PUBLISHED) {
                        if (time() > intval($model->created_at) + (60 * 60 * 12)) {
                            throw new ForbiddenHttpException('Время редактирования материала истекло.');
                        }
                    } else {
                        throw $forbidden;
                    }
                }
            } else {
                throw $forbidden;
            }
        }

        if ($action == 'view') {
            if ($user->role == Rbac::ROLE_MODERATOR) {
                if (!in_array(Section::SECTION_LIBRARY_UUID, $sections)) {
                    throw $forbidden;
                } elseif ($model->type == Material::TYPE_LIBRARY) {
                    if (time() < intval($model->created_at) + (60 * 60 * 12)) {
                        throw $forbidden;
                    }
                }
            } else {
                /** @var MaterialUser $materialUser */
                $materialUser = $model->getMaterialUser($user);

                if (!(
                    $model->created_by == $user->user_uuid ||
                    ($model->type == Material::TYPE_TASK && $materialUser && $materialUser->accessed == MaterialUser::ACCESSED) ||
                    ($model->type == Material::TYPE_LIBRARY && $model->status == Material::STATUS_PUBLISHED &&
                        (
                            $user->role == Rbac::ROLE_COACH ||
                            ($user->role == Rbac::ROLE_EMP && $user->inCoachProgram)
                        )
                    )
                )) {
                    throw $forbidden;
                }
            }
        }

        if ($action == 'delete') {
            if ($model->created_by == $user->user_uuid) {
                if ($model->type == Material::TYPE_LIBRARY) {
                    if ($model->status == Material::STATUS_NOT_PUBLISHED) {
                        if (time() > intval($model->created_at) + (60 * 60 * 12)) {
                            throw new ForbiddenHttpException('Время удаления материала истекло.');
                        }
                    } else {
                        throw $forbidden;
                    }
                }
            } else {
                if (in_array(Section::SECTION_LIBRARY_UUID, $sections)) {
                    if ($model->type == Material::TYPE_LIBRARY) {
                        if (time() < intval($model->created_at) + (60 * 60 * 12)) {
                            throw $forbidden;
                        }
                    }
                } else {
                    throw $forbidden;
                }
            }
        }

        if (in_array($action, ['elected', 'learned'])) {
            if ($model->created_by != $user->user_uuid) {
                if ($model->type == Material::TYPE_LIBRARY && $model->status != Material::STATUS_PUBLISHED) {
                    throw new ForbiddenHttpException('Материал еще не одобрен модератором.');
                } elseif ($model->type == Material::TYPE_TASK) {
                    /** @var MaterialUser $materialUser */
                    $materialUser = $model->getMaterialUser($user);
                    if (!$materialUser || $materialUser->accessed != MaterialUser::ACCESSED) {
                        throw $forbidden;
                    }
                }
            }
        }

        if ($action == 'bind') {
            if ($model->type == Material::TYPE_LIBRARY) {
                throw new ForbiddenHttpException('Это действие запрещено для материала');
            }
        }

        if ($action == 'unbind') {
            if ($model->type == Material::TYPE_LIBRARY) {
                throw new ForbiddenHttpException('Это действие запрещено для материала');
            }
        }
    }
}
