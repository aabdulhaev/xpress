<?php
/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */


use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;
$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'],true);
$calendarLink = Url::to(['calendar/e'],true);
?>
Здравствуйте, <?= Html::encode($employee->first_name) ?>

<?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> принимает Ваш запрос. Вы можете <?= Html::a('связаться', $contactLink) ?> или назначить первую сессию в <?= Html::a('календаре', $calendarLink) ?>.

Желаем Вам плодотворного сотрудничества!

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
