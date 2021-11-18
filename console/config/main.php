<?php

use common\bootstrap\SetUp;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', SetUp::class],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\tests\fixtures',
        ],
    ],
    'components' => [
        'formatter' => [
            'class' => \yii\i18n\Formatter::class,
            'timeZone' => 'Europe/Moscow',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'scriptUrl' => $params['frontHost'],
            'hostInfo' => $params['frontDomain'],
            'baseUrl' => $params['frontHost'],
            'rules' => [
                'confirm/email' => 'confirm/email',
                'confirm/event' => 'confirm/event',
                'confirm/rel' => 'confirm/rel',
                'main/index' => 'main',
            ]
        ],
        'log' => [
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        /*'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api',
                    'pluralize'=>false
                ],
            ],
        ],*/
    ],

    'params' => $params,
];
