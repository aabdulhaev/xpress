<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\filters\Cors;
use common\forms\UserCreateForm;
use common\models\User;
use common\useCases\SignupCase;
use DomainException;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class SignupController extends Controller
{
    use HelperTrait;

    private $useCase;

    public function __construct($id, $module, SignupCase $useCase, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->useCase = $useCase;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = Cors::class;
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['confirm'],
                    'roles' => ['?'],
                ],
                [
                    'allow' => true,
                    'actions' => ['employee', 'mentor'],
                    'roles' => [Rbac::ROLE_HR],
                ],
                [
                    'allow' => true,
                    'roles' => [Rbac::ROLE_ADMIN],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/signup/employee",
     *     tags={"Signup"},
     *     summary="Создание пользователя с ролью <Сотрудник>",
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
     *         description="Информация о созданном сотруднике",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     **/
    public function actionEmployee()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $form = new UserCreateForm();
        $form->role = Rbac::ROLE_EMP;
        $form->client_uuid = $user->client_uuid;

        if ($this->validateBody($form)) {
            try {
                $user = $this->useCase->signup($form);

                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);

                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Post(
     *     path="/signup/mentor",
     *     tags={"Signup"},
     *     summary="Создание пользователя с ролью <Ментор>",
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
     *         description="Информация о созданном сотруднике",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     **/
    public function actionMentor()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $form = new UserCreateForm();
        $form->role = Rbac::ROLE_MENTOR;
        $form->client_uuid = $user->client_uuid;

        if ($this->validateBody($form)) {
            try {
                $user = $this->useCase->signup($form);

                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);

                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Get(
     *     path="/signup/confirm/{token}",
     *     tags={"Signup"},
     *     summary="Активация пользователя по токену",
     *     @OA\Parameter (
     *           name="token",
     *           in="path",
     *           required=true,
     *     ),
     *     @OA\Response(
     *         response = 201,
     *         description = "No content",
     *     ),
     *     @OA\Response(
     *         response = 404,
     *         description = "Токен не найден",
     *     ),
     * )
     */
    public function actionConfirm($token)
    {
        $response = Yii::$app->getResponse();
        try {
            $this->useCase->confirm($token);
            $response->setStatusCode(201);
            return true;
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            $response->setStatusCode(404);
            return $e->getMessage();
        }
    }

    /**
     * Проверяем доступ к сторонним к группам и получение данных об преподавателях и студентах групп
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess(string $action, $model = null, $params = []): void
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (in_array($action, ['lessons', 'homeworks', 'test'], true)) {
            $ids = $user->getGroups()->select('id')->column();
            if (!in_array($model->id, $ids, true)) {
                throw new ForbiddenHttpException('Запрещён доступ к сторонним группам');
            }
        }
        if ($action === 'estimate') {
            $lesson = Lesson::find()->andWhere(['id' => $model->lesson_id, 'teacher_id' => $user->id])->one();
            $hasAccess = $lesson instanceof Lesson;
            if (!$hasAccess) {
                throw new ForbiddenHttpException('Запрещёно выставлять оценки за сторонние лекции.');
            }

            if (!$group = $lesson->getGroup()->one()) {
                throw new ForbiddenHttpException('Запрещёно выставлять оценки за сторонние лекции.');
            }

            $isStudent = $group->getStudents()->andWhere(['user.id' => $model->student_id])->exists();

            if (!$isStudent) {
                throw new ForbiddenHttpException('Запрещёно выставлять оценки студентам других групп.');
            }
        }
    }
}
