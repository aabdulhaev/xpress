<?php

use common\components\Google;

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'name' => 'EMPLITUDE',
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@console/migrations',
                '@yii/rbac/migrations',
            ]
        ]
    ],
    'bootstrap'  => [
        'queue', // The component registers its own console commands
    ],
    'language' => 'ru-RU',
    'aliases'       => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@storageRoot' => $params['storagePath'],
        '@storage' => $params['storageHostInfo'],
    ],
    'vendorPath'    => dirname(dirname(__DIR__)) . '/vendor',
    'components'    => [
        'formatter' => [
            'class' => \yii\i18n\Formatter::class,
            'timeZone' => 'Europe/Moscow'
        ],
        'cache'       => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
        ],
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => 'redis',
            'channel' => 'queue',
            'as log' => 'yii\queue\LogBehavior'// Queue channel key
        ],
        'google' => [
            'class' => Google::class,
            'authConfigPath' => $_ENV['GOOGLE_AUTH_CONFIG_PATH'],
            'redirectUri' => $_ENV['GOOGLE_REDIRECT_URI']
        ]
    ],
    'params'        => $params
];
