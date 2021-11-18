<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */

$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'],true);
$roleName = $checkMentor ? 'ментора' : 'коуча';
?>
Здравствуйте, <?= Html::encode($employee->first_name) ?>

К сожалению, <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> не сможет принять Ваш запрос.

<?php if (!empty($comment)): ?>
    "<?= nl2br(Html::encode($comment)) ?>"
<?php endif; ?>

Выберите нового <?=$roleName?> из предложенного <?=Html::a('списка',$contactLink)?>.

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
