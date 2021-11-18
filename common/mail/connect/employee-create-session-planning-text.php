<?php
/* @var $this yii\web\View */
/* @var $employee \common\models\User */
/* @var $mentor \common\models\User */
/* @var $dateStart string */

use yii\helpers\Html;
use yii\helpers\Url;

$profileLink = Url::to(['profile/'.$employee->user_uuid], true);
$calendarLink = Url::to(['calendar/c'],true);
?>
Здравствуйте, <?= Html::encode($mentor->first_name) ?>

У Вас новый запрос на проведение сессии <?=$dateStart ?> (GMT +3) от <?= Html::a(Html::encode($employee->fullName), $profileLink) ?>.

Перейдите в <?= Html::a('календарь', $calendarLink) ?> для подтверждения или предложите альтернативный слот.

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
