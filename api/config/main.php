<?php

use common\bootstrap\SetUp;
use api\modules\v1\Module;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\JsonParser;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$rules = require __DIR__ . '/rules.php';

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'basePath' => '@api/modules/v1',
            'class' => Module::class
        ]
    ],
    'bootstrap' => [
        'log',
        SetUp::class,
        [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ],
    ],
    'components' => [
        'user' => [
            'class' => 'api\components\User',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
            'on afterLogin' => static function ($event) {
                if (!Yii::$app->user->isGuest) {
                    Yii::$app->timeZone = Yii::$app->user->identity->time_zone;
                }
            }
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:429',
                        'yii\web\HttpException:401'
                    ]
                ]
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
        ],
        'request' => [
            'parsers' => [
                'application/json' => JsonParser::class,
                'multipart/form-data' => yii\web\MultipartFormDataParser::class
            ],
            'enableCsrfCookie' => false
        ],
        'response' => [
            'class' => Response::class,
            /*'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && ($exception = Yii::$app->getErrorHandler()->exception) !== null) {
                    $response->data = [
                        'error' => $response->data,
                    ];
                }
            },*/
        ],
    ],
    'as corsFilter' => [
        'class' => \common\filters\Cors::class
    ],
    'as authenticator' => [
        'class' => HttpBearerAuth::class,
        'except' => [
            'site/index',
            'v1/docs/*',
            'v1/auth/login',
            'v1/auth/reset',
            'v1/auth/confirm',
            'v1/signup/confirm',
            'v1/request/client',
            'v1/request/coach',
            'v1/profile/options',
            'v1/user/options',
            'v1/mentor/options',
            'v1/hook/end',
            'v1/competency-profile/options',
            'v1/meeting/view',
            'v1/meeting/group-join',
            'v1/meeting/make-confirm',
            'v1/meeting/check-confirm',
        ],
    ],
    /*'as access' => [
        'class' => AccessControl::class,
        'except' => [
            'site/index',
            'v1/auth/login',
            'v1/auth/reset',
            'v1/auth/confirm',
            'v1/signup/confirm'
        ],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],*/
    /*'as exceptionFilter' => [
        'class' => 'filsh\yii2\oauth2server\filters\ErrorToExceptionFilter',
    ],*/
    'params' => $params,
];
