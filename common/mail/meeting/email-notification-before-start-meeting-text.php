<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 * @var $token string
 */

$link = $meeting->prepareJoinLink($token);
?>

Добрый день!

Напоминаем Вам, что завтра пройдет вебинар '<?= Html::encode($meeting->title) ?>'.

<?= Html::encode($meeting->description) ?>

Ждем Вас <?= $meeting->formatStartDate() ?> в <?= $meeting->formatStartTime() ?> (GMT +3)

Для подключения к видео-конференции перейдите, пожалуйста, по ссылке.

<?= Html::a('ПОДКЛЮЧИТЬСЯ', $link) ?>

Перед началом сессии убедитесь, что в настройках Вашего браузера предоставлен доступ к микрофону и камере.

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>.

