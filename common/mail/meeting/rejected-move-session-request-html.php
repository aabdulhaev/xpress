<?php

/**
 * @var $session TrainingSession
 * @var $sender User
 * @var $recipient User
 * @var $comment string
 * @var $fromDateTime string
 * @var $toDateTime string
 */

use common\access\Rbac;
use common\models\TrainingSession;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$formatter = Yii::$app->formatter;
if ($recipient->role === Rbac::ROLE_EMP) {
    $calendarLink = Url::to(['calendar/e'], true);
} elseif ($recipient->role === Rbac::ROLE_COACH) {
    $calendarLink = Url::to(['calendar/c'], true);
} else {
    $calendarLink = Url::to(['calendar/m'], true);
}
$profileLink = Url::to(['profile/' . $sender->user_uuid], true);
$role = $sender->isUserRoleCoach() ? 'коуч' : 'ментор';

$formatter = Yii::$app->formatter;

$calendarLink = Url::to(['calendar/m'], true);
if ($recipient->role === Rbac::ROLE_EMP) {
    $calendarLink = Url::to(['calendar/e'], true);
} elseif ($recipient->role === Rbac::ROLE_COACH) {
    $calendarLink = Url::to(['calendar/c'], true);
}

?>

<?=$this->render('@common/mail/layouts/_header')?>


<tr>
    <td align="left" valign="middle" style="padding-top:24px;">
            <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
              Здравствуйте, <?= Html::encode($recipient->first_name) ?>
            </span>
    </td>
</tr>

<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">Ваш <?= $role ?> <?=
            Html::a(Html::encode($sender->fullName), $profileLink)
        ?> не может провести сессию в предложенные дату и время и подтвердить перенос.</span>
    </td>
</tr>

<?php if (!empty($comment)) : ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">"<?=
                nl2br(Html::encode($comment, false))
            ?>"</span>
        </td>
    </tr>
<?php endif; ?>

<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text"
              style="font-size:16px;color:#546B82;"
        >Если перенос для Вас еще актуален, отмените ранее подтвержденную сессию в <?=
            Html::a('календаре', $calendarLink)
        ?> и выберете свободный слот коуча, отмеченный зеленым.</span>
    </td>
</tr>

<?=$this->render('@common/mail/layouts/_footer')?>



