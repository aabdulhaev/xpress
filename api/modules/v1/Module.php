<?php

namespace api\modules\v1;

use Yii;
use api\modules\v1\models\ApiUserIdentity;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    public function init(): void
    {
        parent::init();
        Yii::$app->user->identityClass = ApiUserIdentity::class;
        Yii::$app->user->enableSession = false;
        Yii::$app->user->loginUrl = null;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];
        return $behaviors;
    }
}
