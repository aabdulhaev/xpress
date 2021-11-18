<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\filters\Cors;
use common\forms\AssignProgramForm;
use common\forms\ConnectRequestForm;
use common\forms\ContactForm;
use common\forms\EmployeeUpdateForm;
use common\forms\InviteForm;
use common\models\User;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserRepository;
use common\useCases\UserManageCase;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class EventController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
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
                    'roles' => [Rbac::ROLE_ADMIN],
                ],
            ],
        ];
        return $behaviors;
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
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        throw new ForbiddenHttpException('доступ к контроллеру запрещен');
    }
}
