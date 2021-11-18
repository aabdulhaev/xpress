<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../../common/config/test.php',
    require __DIR__ . '/../../common/config/test-local.php',

    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test-local.php',
    [
        'components' => [
            'request' => [
                'cookieValidationKey' => 'mm9nfPFZFUu8y8rGGCoN2hoLldEClegn',
            ],
        ],
    ]
);
