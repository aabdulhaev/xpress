<?php

namespace api\modules\v1\controllers;

use api\components\RestValidationError;
use api\components\User;
use common\filters\Cors;
use common\forms\LoginForm;
use common\forms\PasswordResetRequestForm;
use common\forms\ResetPasswordForm;
use common\useCases\PasswordResetCase;
use Yii;
use yii\rest\ActiveController;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

/**
 * Class UserController
 * @package api\modules\v1\controllers
 *
 * @noinspection PhpUnused
 */
class AuthController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;

    public function __construct($id, $module, PasswordResetCase $useCase, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->useCase = $useCase;
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     operationId="actionLogin",
     *     tags={"Login"},
     *     summary="Авторизация и получение токена.",
     *     @OA\RequestBody(
     *         description="Объект для авторизации",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LoginForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неверный запрос"
     *     )
     * )
     *
     * @return array|LoginForm
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        $form = new LoginForm();
        $form->load(Yii::$app->request->getBodyParams(), '');

        if ($token = $form->login()) {
            return ['access_token' => $token];
        }

        return $form;
    }

    public function actionReset()
    {
        $form = new PasswordResetRequestForm();
        if ($this->validateBody($form)) {
            try {
                $this->useCase->request($form);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }

    public function actionConfirm($token)
    {
        $form = new ResetPasswordForm($token);

        if ($this->validateBody($form)) {
            try {
                $this->useCase->reset($token, $form);
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }

        return $form;
    }


    protected function verbs()
    {
        return [
            'login' => ['POST', 'OPTIONS'],
            'reset' => ['POST', 'OPTIONS'],
            'confirm' => ['POST', 'OPTIONS']
        ];
    }
}
