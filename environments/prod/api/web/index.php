<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../../.env');

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

$application = new yii\web\Application($config);

putenv("BBB_SERVER_BASE_URL=" . Yii::$app->params['BBB_SERVER_BASE_URL']);
putenv("BBB_SECRET=" . Yii::$app->params['BBB_SECRET']);
putenv("BBB_MEETING_BASE_URL=" . Yii::$app->params['BBB_MEETING_BASE_URL']);
putenv("BBB_HOOK_URL=" . Yii::$app->params['BBB_HOOK_URL']);
putenv("BBB_LOGOUT_URL=" . Yii::$app->params['BBB_LOGOUT_URL']);

/**
 * Init application constants
 */
defined('YII_DEBUG') or define('YII_DEBUG', $_ENV['YII_DEBUG']);
defined('YII_ENV') or define('YII_ENV', !empty($_ENV['YII_ENV']) ? $_ENV['YII_ENV'] : 'prod');
defined('API_HOST') or define('API_HOST', !empty($_ENV['API_HOST']) ? $_ENV['API_HOST'] : 'https://api-xpress.loc/v1/');

$application->run();
