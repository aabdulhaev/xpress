<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\RequestSearch;
use common\access\Rbac;
use common\filters\Cors;
use common\forms\RequestForm;
use DomainException;
use common\models\Request;
use common\repositories\RequestRepository;
use common\useCases\RequestCase;
use Yii;
use yii\data\ActiveDataFilter;
use yii\filters\{AccessControl};
use yii\rest\Controller;
use yii\rest\IndexAction;
use yii\rest\ViewAction;
use yii\web\BadRequestHttpException;

/**
 * Class RequestController
 * @package api\modules\v1\controllers
 *
 * @noinspection PhpUnused
 */
class RequestController extends Controller
{
    use HelperTrait;

    public $modelClass = 'common\models\Request';

    private $useCase;
    private $repo;

    public function __construct($id, $module, RequestCase $useCase, RequestRepository $repo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->useCase = $useCase;
        $this->repo = $repo;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = Cors::class;
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['coach', 'client'],
                    'roles' => ['?'],
                ],
                [
                    'allow' => true,
                    'roles' => [Rbac::ROLE_ADMIN],
                ],
            ],
        ];
        return $behaviors;
    }


    public function actionCoach()
    {
        $form = new RequestForm(Request::TYPE_COACH);
        if ($this->validateBody($form)) {
            try {
                $this->useCase->create($form);
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    public function actionClient()
    {
        $form = new RequestForm(Request::TYPE_CLIENT);
        if ($this->validateBody($form)) {
            try {
                $this->useCase->create($form);
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } catch (DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    /**
     * Изменяет статус заявки
     * @return Request
     * @noinspection PhpUnused
     * @throws BadRequestHttpException
     */
    public function actionApprove($id)
    {
        $model = $this->repo->get($id);
        try {
            $this->useCase->approve($model);
        } catch (DomainException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }

        return $model;
    }

    /**
     * Изменяет статус заявки
     * @return Request
     * @noinspection PhpUnused
     * @throws BadRequestHttpException
     */
    public function actionDecline($id)
    {
        $model = $this->repo->get($id);
        try {
            $this->useCase->decline($model);
        } catch (DomainException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }

        return $model;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['view'] = [
            'class' => ViewAction::class,
            'modelClass' => $this->modelClass,
        ];
        $actions['index'] = [
            'class' => IndexAction::class,
            'modelClass' => $this->modelClass,
            'dataFilter' => [
                'class' => ActiveDataFilter::class,
                'searchModel' => RequestSearch::class,
                'queryOperatorMap' => [
                    'LIKE' => 'ILIKE',
                ]
            ]
        ];
        return $actions;
    }


    protected function verbs(): array
    {
        return [

            'view' => ['GET', 'OPTIONS'],
            'index' => ['GET', 'HEAD', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS'],
            'approve' => ['GET', 'OPTIONS'],
            'decline' => ['GET', 'OPTIONS'],
            'coach' => ['POST', 'OPTIONS'],
            'client' => ['POST', 'OPTIONS']
        ];
    }
}
