<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\access\Rbac;
use common\models\UserCompetencyProfile;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\DeleteAction;

class CompetencyProfileController extends ActiveController
{
    public $modelClass = UserCompetencyProfile::class;

    public function behaviors()
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
                    'actions'   => ['delete',],
                    'roles' => [Rbac::ROLE_HR],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'view' => false,
                'create' => false,
                'update' => false,
                'delete' => [
                    'class' => DeleteAction::class,
                    'modelClass' => $this->modelClass
                ]
            ]
        );
    }
}
