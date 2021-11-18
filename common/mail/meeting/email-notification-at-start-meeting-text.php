<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 * @var $token string
 */

$link = $meeting->prepareJoinLink($token);
?>

Добрый день!

Вебинар '<?= Html::encode($meeting->title) ?>' начался, ждем Вас!

Для подключения к видео-конференции перейдите, пожалуйста, по ссылке.

<?= Html::a('ПОДКЛЮЧИТЬСЯ', $link) ?>

Перед началом сессии убедитесь, что в настройках Вашего браузера предоставлен доступ к микрофону и камере.

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>.

