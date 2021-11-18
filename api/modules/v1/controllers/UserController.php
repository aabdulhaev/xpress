<?php

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\actions\user\{CreateAction, UpdateAction};
use api\modules\v1\models\UserSearch;
use common\access\Rbac;
use common\components\helpers\UserCoachAccessHelper;
use common\components\helpers\UserEmployeeAccessHelper;
use common\components\helpers\UserHrRoleAccessHelper;
use common\components\helpers\UserMentorAccessHelper;
use common\forms\StatusUpdateForm;
use common\models\User;
use common\repositories\UserRepository;
use common\useCases\UserManageCase;
use DomainException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yii;
use yii\data\ActiveDataFilter;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\ViewAction;
use yii\web\{BadRequestHttpException, ForbiddenHttpException};

/**
 * @OA\Get(
 *     path="/user",
 *     tags={"User"},
 *     summary="Возвращает список пользователей",
 *     @OA\Response(
 *         response = 200,
 *         description = "Список пользователей",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     ),
 * )
 *
 * @OA\Get(
 *     path="/user/view/{id}",
 *     tags={"User"},
 *     summary="Возвращает информацию о пользователе с идентификатором <id>",
 *     @OA\Parameter (
 *           name="id",
 *           in="path",
 *           required=true,
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "Информация о пользователе",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     ),
 * )
 *
 * @OA\Delete (
 *     path="/user/delete/{id}",
 *     tags={"User"},
 *     summary="Удаление пользователя с идентификатором <id>",
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
 *
 * @OA\Patch(
 *     path="/user/change-status/{id}",
 *     tags={"User"},
 *     summary="Изменение статуса пользователя с идентификатором <id>",
 *     @OA\Parameter (
 *           name="id",
 *           in="path",
 *           required=true,
 *     ),
 *     @OA\RequestBody(
 *         description="Данные для изменение статуса",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(ref="#/components/schemas/StatusUpdateForm")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description = "Информация о пользователе",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     )
 * )
 *
 * @OA\Get(
 *     path="/user/roles",
 *     tags={"User"},
 *     summary="Возвращает список ролей.",
 *     @OA\Response(
 *         response = 200,
 *         description = "Список ролей",
 *     ),
 * )
 */
class UserController extends ActiveController
{
    use HelperTrait;

    public $modelClass = 'common\models\User';

    private $manageCase;
    private $repo;

    public function __construct(
        $id,
        $module,
        UserManageCase $manageCase,
        UserRepository $repo,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->manageCase = $manageCase;
        $this->repo = $repo;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [Rbac::ROLE_ADMIN, Rbac::ROLE_MODERATOR],
                ],
                [
                    'allow' => true,
                    'actions' => ['create-mentor', 'update'],
                    'roles' => [Rbac::ROLE_HR],
                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'matchCallback' => function () {
                        $this->checkAccess($this->action->id);
                        return true;
                    }
                ],
            ],
        ];
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }

    public function actions(): array
    {
        $actions = ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'dataFilter' => [
                        'class' => ActiveDataFilter::class,
                        'searchModel' => UserSearch::class,
                        'queryOperatorMap' => [
                            'LIKE' => 'ILIKE',
                        ],
                        'attributeMap' => [
                            'first_name' => "CONCAT(first_name, ' ', last_name)"
                        ]
                    ]
                ],
                'create' => [
                    'class' => CreateAction::class,
                    'modelClass' => $this->modelClass
                ],
                'update' => [
                    'class' => UpdateAction::class,
                    'modelClass' => $this->modelClass,
                ],
                'view' => [
                    'class' => ViewAction::class,
                    'modelClass' => $this->modelClass
                ],
            ]
        );

        return $actions;
    }

    /**
     * Возвращает список ролей
     * @return array|null
     */
    public function actionRoles()
    {
        $availableRoles = [];

        if (($roles = Yii::$app->authManager->getRoles()) !== null) {
            foreach ($roles as $role) {
                $availableRoles[] = [
                    'name' => $role->name,
                    'description' => $role->description
                ];
            }
        }

        return $availableRoles;
    }

    /**
     * Изменение статуса пользователя
     * @param $id
     * @return StatusUpdateForm|ActiveRecord
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionChangeStatus($id)
    {
        $user = $this->repo->get($id);
        $this->checkAccess($this->action->id, $user);

        $form = new StatusUpdateForm($user);

        if ($this->validateBody($form)) {
            try {
                $this->manageCase->editStatus($form);
                $user->refresh();
                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD', 'OPTIONS'],
            'view' => ['GET', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['POST', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS'],
            'roles' => ['GET', 'OPTIONS'],
            'set-role' => ['POST', 'OPTIONS'],
            'change-status' => ['PATCH', 'OPTIONS'],
            'password' => ['PATCH', 'OPTIONS'],
        ];
    }

    /**
     * Проверяем доступ к действиям
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var User $authUser */
        $authUser = \Yii::$app->user->identity;

        if (($action === 'change-status') && ($model->user_uuid === $authUser->user_uuid)) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        if ($action == 'view') {
            /** @var User $user */
            $user = $this->repo->get(Yii::$app->request->get('id'));
            if (
                $authUser->isUserRoleEmployee() && !UserEmployeeAccessHelper::checkAccess($authUser, $user)
                || ($authUser->isUserRoleMentor() && !UserMentorAccessHelper::checkAccess($authUser, $user))
                || ($authUser->isUserRoleCoach() && !UserCoachAccessHelper::checkAccess($authUser, $user))
                || ($authUser->isUserRoleHr() && !UserHrRoleAccessHelper::checkAccess($authUser, $user))
            ) {
                throw new AccessDeniedException("Доступ запрещен.");
            }
        }
    }
}
