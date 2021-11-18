<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 */

use common\access\Rbac;
use yii\helpers\Html;
use yii\helpers\Url;

if($recipient->role === Rbac::ROLE_EMP){
    $calendarLink = Url::to(['calendar/e'],true);
}elseif ($recipient->role === Rbac::ROLE_COACH){
    $calendarLink = Url::to(['calendar/c'],true);
}else{
    $calendarLink = Url::to(['calendar/m'],true);
}

?>
Здравствуйте, <?= Html::encode($recipient->first_name) ?>

Ваша сессия с <?= Html::encode($sender->fullName)?> на <?=Yii::$app->formatter->asDatetime($session->start_at_tc,'php:d.m.Y')?> в <?=Yii::$app->formatter->asDatetime($session->start_at_tc,'php:H:i')?> (GMT +3) подтверждена.

Вы можете перенести или отменить сессию не позднее чем за 8 часов до ее начала в разделе  <?= Html::a('календарь', $calendarLink) ?>.

До встречи на сессии!


С Уважением,
команда поддержки пользователей EMPLITUDE
