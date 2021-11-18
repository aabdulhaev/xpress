<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\components\helpers\UserHelper;
use common\forms\AddVideoPresentationCoachForm;
use common\forms\AssignSubjectForm;
use common\forms\AvatarForm;
use common\forms\UserUpdatePasswordForm;
use common\models\Competence;
use common\models\Section;
use common\models\Subject;
use common\models\User;
use common\repositories\UserRepository;
use common\useCases\ProfileManageCase;
use DomainException;
use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class ProfileController extends Controller
{
    use HelperTrait;

    private $useCase;
    private $repo;

    public function __construct($id, $module, ProfileManageCase $useCase, UserRepository $repo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->useCase = $useCase;
        $this->repo = $repo;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }

    protected function verbs(): array
    {
        return [
            'view' => ['GET', 'OPTIONS'],
            'stats' => ['GET', 'OPTIONS'],
            'subjects' => ['GET', 'OPTIONS'],
            'competencies' => ['GET', 'OPTIONS'],
            'all-subjects' => ['GET', 'OPTIONS'],
            'all-competencies' => ['GET', 'OPTIONS'],
            'all-sections' => ['GET', 'OPTIONS'],
            'remove-avatar' => ['DELETE', 'OPTIONS'],
            'change-avatar' => ['POST', 'OPTIONS'],
            'password' => ['PATCH', 'OPTIONS'],
            'assign-subject' => ['POST', 'OPTIONS'],
            'upload-video-presentation-coach' => ['POST', 'OPTIONS'],
            'delete-video-presentation-coach' => ['POST', 'OPTIONS']
        ];
    }

    public function findModel(): User
    {
        return $this->repo->get(Yii::$app->user->getId());
    }

    /**
     * @OA\Get(
     *     path="/profile/view",
     *     tags={"Profile"},
     *     summary="Получение информации о пользователе",
     *     @OA\Response(
     *         response = 200,
     *         description = "Информация о пользователе",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */
    public function actionView(): User
    {
        return $this->findModel();
    }

    /**
     * @OA\Get(
     *     path="/profile/stats",
     *     tags={"Profile"},
     *     summary="Получение программ к которым подключён пользователь",
     *     @OA\Response(
     *         response = 200,
     *         description = "Массив программ",
     *     ),
     * )
     */
    public function actionStats(): array
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        return $user->programAssignments;
    }

    /**
     * @OA\Get(
     *     path="/profile/subjects",
     *     tags={"Profile"},
     *     summary="Получение специализаций пользователя",
     *     @OA\Response(
     *         response = 200,
     *         description = "Массив специализаций",
     *     ),
     * )
     */
    public function actionSubjects(): array
    {
        /**
         * @var User $user
         */
        $user = Yii::$app->user->identity;
        return $user->subjects;
    }

    /**
     * @OA\Get(
     *     path="/profile/competencies",
     *     tags={"Profile"},
     *     summary="Получение компетенций пользователя",
     *     @OA\Response(
     *         response = 200,
     *         description = "Массив компетенций",
     *     ),
     * )
     */
    public function actionCompetencies(): array
    {
        /**
         * @var User $user
         */
        $user = Yii::$app->user->identity;
        return $user->competencies;
    }

    /**
     * @OA\Get(
     *     path="/profile/all-subjects",
     *     tags={"Profile"},
     *     summary="Получение всех специализаций доступных пользователю",
     *     @OA\Response(
     *         response = 200,
     *         description = "Массив специализаций",
     *     ),
     * )
     */
    public function actionAllSubjects(): array
    {
        $uuids = UserHelper::getUniqueValues(Subject::find()->all(), 'subject_uuid');
        return Subject::find()->andWhere(['in', 'subject_uuid', $uuids])->all();
    }

    /**
     * @OA\Get(
     *     path="/profile/all-competencies",
     *     tags={"Profile"},
     *     summary="Получение всех компетенций доступных пользователю",
     *     @OA\Response(
     *         response = 200,
     *         description = "Массив компетенций",
     *     ),
     * )
     */
    public function actionAllCompetencies(): array
    {
        $uuids = UserHelper::getUniqueValues(Competence::find()->all(), 'competence_uuid');
        return Competence::find()->andWhere(['in', 'competence_uuid', $uuids])->all();
    }

    /**
     * @OA\Get(
     *     path="/profile/all-sections",
     *     tags={"Profile"},
     *     summary="Получение всех разделов доступных пользователю",
     *     @OA\Response(
     *         response = 200,
     *         description = "Массив разделов",
     *     ),
     * )
     */
    public function actionAllSections(): array
    {
        return Section::find()->all();
    }

    /**
     * @OA\Post(
     *     path="/profile/change-avatar",
     *     tags={"Profile"},
     *     summary="Загрузка нового аватара пользователя",
     *     @OA\RequestBody(
     *         description="Данные для изменении пароля",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/AvatarForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Информация о пользователе",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     * */
    public function actionChangeAvatar()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $form = new AvatarForm();

        if ($form->load(['AvatarForm' => ['avatar' => '']]) && $form->validate()) {
            try {
                $this->useCase->editAvatar($user->user_uuid, $form);
                $user->refresh();
                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Delete (
     *     path="/rofile/remove-avatar",
     *     tags={"Profile"},
     *     summary="Удаление аватара пользователя",
     *     @OA\Response(
     *         response=204,
     *         description="No content",
     *     )
     * )
     */
    public function actionRemoveAvatar()
    {
        try {
            $this->useCase->removeAvatar(Yii::$app->user->getId());
            Yii::$app->response->setStatusCode(204);
        } catch (DomainException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }
    }

    /**
     * @OA\Patch(
     *     path="/profile/password",
     *     tags={"Profile"},
     *     summary="Изменение пароля пользователя",
     *     @OA\RequestBody(
     *         description="Данные для изменении пароля",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/UserUpdatePasswordForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Информация о пользователе",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     * */
    public function actionPassword()
    {
        $form = new UserUpdatePasswordForm();

        if ($this->validateBody($form)) {
            try {
                $this->useCase->setPassword(Yii::$app->user->getId(), $form);
                return Yii::$app->user->identity;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\POST(
     *     path="/profile/assign-subject",
     *     tags={"Profile"},
     *     summary="Добавление специализаций пользователя",
     *     @OA\RequestBody(
     *         description="Массив специализаций",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/AssignSubjectForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description = "Информация о пользователе",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     * */
    public function actionAssignSubject()
    {
        /**
         * @var User $user
         */
        $user = Yii::$app->user->identity;
        $form = new AssignSubjectForm($user);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->assignSubjects($user->user_uuid, $form);
                $user->refresh();
                Yii::$app->response->setStatusCode(201);
                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Post (
     *     path="/profile/upload-video-presentation-coach",
     *     tags={"Profile"},
     *     summary="Загрузка видео-презентации для коуча",
     *     @OA\RequestBody(
     *         description="Параметры",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/AddVideoPresentationCoachForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description = "Информация о пользователе",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     */
    public function actionUploadVideoPresentationCoach()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user->isUserRoleCoach()) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        $form = new AddVideoPresentationCoachForm();
        if ($this->validateBody($form)) {
            try {
                $this->useCase->uploadVideoPresentationCoach($user, $form);
                $user->refresh();

                Yii::$app->response->setStatusCode(201);
                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    /**
     * @OA\Post (
     *     path="/profile/delete-video-presentation-coach",
     *     tags={"Profile"},
     *     summary="Удаление видео-презентацию коуча",
     *     @OA\Response(
     *         response=201,
     *         description = "Информация о пользователе",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     */
    public function actionDeleteVideoPresentationCoach()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user->isUserRoleCoach()) {
            throw new ForbiddenHttpException('Вы не можете выполнять данное действие.');
        }

        try {
            $this->useCase->deleteVideoPresentationCoach($user);
            $user->refresh();

            Yii::$app->response->setStatusCode(201);
            return $user;
        } catch (DomainException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }
    }
}
