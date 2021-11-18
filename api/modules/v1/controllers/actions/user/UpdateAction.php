<?php

namespace api\modules\v1\controllers\actions\user;

use api\modules\v1\controllers\HelperTrait;
use common\access\Rbac;
use common\forms\UserUpdateForm;
use common\repositories\UserRepository;
use common\useCases\SignupCase;
use common\useCases\UserManageCase;
use DomainException;
use yii\web\BadRequestHttpException;

/**
 * @OA\Post(
 *     path="/user/update/{id}",
 *     tags={"User"},
 *     summary="Редактирование пользователя с идентификатором <id>",
 *     @OA\Parameter (
 *           name="id",
 *           in="path",
 *           required=true,
 *     ),
 *     @OA\RequestBody(
 *         description="Данные для редактирования пользователя",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(ref="#/components/schemas/UserUpdateForm")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description = "Информация об обновлённом пользователе",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     )
 * )
 * */
class UpdateAction extends \yii\rest\UpdateAction
{
    use HelperTrait;

    private $useCase;
    private $manageCase;
    private $repo;

    public function __construct(
        $id,
        $controller,
        SignupCase $useCase,
        UserManageCase $manageCase,
        UserRepository $repo,
        $config = []
    )
    {
        parent::__construct($id, $controller, $config);
        $this->useCase = $useCase;
        $this->manageCase = $manageCase;
        $this->repo = $repo;
    }

    public function run($id)
    {
        $user = $this->repo->get($id);
        $form = new UserUpdateForm($user);
        $form->authUser = \Yii::$app->user->identity;

        if ($this->validateBody($form)) {
            try {
                $this->useCase->assignSubjects($form->user, $form->subjects);
                $this->useCase->assignSections($form->user, $form->sections);
                $this->useCase->assignCompetencies($form->user, $form->competencies);

                if ($user->role === Rbac::ROLE_EMP) {
                    $this->manageCase->manageUserPrograms($form);
                } elseif (in_array($user->role, [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR])) {
                    $this->manageCase->manageMentorPrograms($form);
                }

                $this->manageCase->edit($form);
                $this->manageCase->saveCompetencyProfile($form);
                $user->refresh();
                return $user;
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }
}
