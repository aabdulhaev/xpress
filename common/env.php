<?php
/**
 * Load application environment from .env file
 */

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

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
