<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 * @var $userMeeting \common\models\UserMeeting
 * @var $previousStartDate string
 * @var $previousStartTime string
 */

$link = $meeting->prepareJoinLink($userMeeting->token);
?>

Добрый день!

Хотим сообщить, что вебинар '<?= Html::encode($meeting->title) ?>' переносится c <?= $previousStartDate ?> в <?= $previousStartTime ?> (GMT +3) на <?= $meeting->formatStartDate() ?> в <?= $meeting->formatStartTime() ?> (GMT +3)

Для подключения к видео-конференции перейдите, пожалуйста, по ссылке.

<?= Html::a('ПОДКЛЮЧИТЬСЯ', $link) ?>

Перед началом сессии убедитесь, что в настройках Вашего браузера предоставлен доступ к микрофону и камере.

Приносим свои извинения!

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>.

