<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\controllers\actions\client\CreateAction;
use api\modules\v1\controllers\actions\client\UpdateAction;
use api\modules\v1\controllers\actions\client\ViewAction;
use api\modules\v1\models\ClientSearch;
use common\access\Rbac;
use common\models\Client;
use common\repositories\ClientRepository;
use common\useCases\ClientCase;
use yii\data\ActiveDataFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class ClientController extends ActiveController
{
    use HelperTrait;

    public $modelClass = Client::class;

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [Rbac::ROLE_ADMIN],
                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions(): array
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'dataFilter' => [
                        'class' => ActiveDataFilter::class,
                        'searchModel' => ClientSearch::class,
                        'queryOperatorMap' => [
                            'LIKE' => 'ILIKE',
                        ]
                    ]
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
                'delete' => false
            ]
        );
    }
}
