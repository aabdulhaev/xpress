<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 */

?>

Добрый день!

Хотим сообщить, что вебинар '<?= Html::encode($meeting->title) ?>', запланированный на <?= $meeting->formatStartDate() ?> в <?= $meeting->formatStartTime() ?> (GMT +3), отменен по техническим причинам.

Приносим свои извинения!

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>.

