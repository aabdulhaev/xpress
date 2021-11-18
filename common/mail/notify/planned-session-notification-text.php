<?php

/**
 * @var $recipient User
 * @var $coach User
 * @var $training TrainingSession
 */

use common\models\TrainingSession;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$hours = strtotime($training->start_at_tc) - time() < 24 * 60 * 60 ? '24 часа' : '48 часов';

$profileLink = Url::to(['profile/' . $coach->user_uuid], true);

?>
Здравствуйте, <?= Html::encode($recipient->first_name) ?>

Ваша сессия с <?= Html::a($coach->fullName, $profileLink) ?> на платформе <?= Yii::$app->name ?> начнётся через <?= $hours ?>
А пока - проверьте наличие доступа к камере и микрофону в настройках Вашего браузера.

С Уважением, команда поддержки пользователей <?= Yii::$app->name ?>
