<?php

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\components\Google;
use common\filters\Cors;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class GoogleController extends Controller
{
    /**
     * @var Google
     */
    private $google;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->google = Yii::$app->google;
    }

    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'corsFilter' => Cors::class,
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => [Rbac::ROLE_COACH, Rbac::ROLE_MENTOR],
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionAuth(): string
    {
        return $this->google->getAuth()->getAuthUrl();
    }

    public function actionProcessCode(string $code, string $user_uuid): void
    {
        $accessToken = $this->google->getAuth()->getAccessTokenByCode($code);
        $user = User::findOne(['user_uuid' => $user_uuid]);
        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }
        $user->setGoogleAccessToken($accessToken);
        $user->save(false);
    }
}