<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 * @var $token string
 */

$link = $meeting->prepareConfirmLink($token);
?>

Добрый день!

Приглашаем Вас на вебинар '<?= Html::encode($meeting->title) ?>'.

<?= Html::encode($meeting->description) ?>

Вебинар состоится <?= $meeting->formatStartDate() ?> в <?= $meeting->formatStartTime() ?> (GMT +3)

Будем благодарны, если Вы подтвердите свое участие.

<?= Html::a('ПОДТВЕРДИТЬ УЧАСТИЕ', $link) ?>

Ссылка на подключение к видео-конференции будет направлена за 24 часа до начала.

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>.

