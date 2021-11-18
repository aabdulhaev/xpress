<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */
?>
<?php
$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;
$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'],true);
$roleName = $mentor->role === Rbac::ROLE_MENTOR ? 'ментора' : 'коуча';
?>
Здравствуйте, <?= Html::encode($employee->first_name) ?>

Вы приостановили сессии с <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?>. Если Вы сделали это по ошибке, свяжитесь со службой поддержки - <?=\Yii::$app->params['supportEmail']?>

Вы можете выбрать нового <?= Html::a($roleName, $contactLink) ?> для продолжения сессий на платформе.

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
