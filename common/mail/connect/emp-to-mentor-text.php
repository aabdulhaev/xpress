<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */

$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;

$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'],true);
$calendarLink = Url::to(['calendar/e'],true);

$role = $checkMentor ? 'ментор' : 'коуч';
?>
Здравствуйте <?= Html::encode($employee->fullName) ?>,
Вам назначен <?= $role ?> <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?>.
Вы можете <?= Html::a('связаться', $contactLink) ?> или назначить
первую сессию в <?= Html::a('календаре', $calendarLink) ?>.

Желаем Вам плодотворного сотрудничества!

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
