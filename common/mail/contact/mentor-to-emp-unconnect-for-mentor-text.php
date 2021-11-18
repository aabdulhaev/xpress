<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */

$profileLink = Url::to(['profile/'.$employee->user_uuid], true);
?>
Здравствуйте, <?= Html::encode($mentor->first_name) ?>

Вы приостановили сессии с <?= Html::a(Html::encode($employee->fullName), $profileLink) ?>. Если Вы сделали это по ошибке, свяжитесь со службой поддержки - <?=\Yii::$app->params['supportEmail']?>

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
