<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 */


use common\access\Rbac;
use yii\helpers\Html;
use yii\helpers\Url;

if ($recipient->role === Rbac::ROLE_EMP) {
    $calendarLink = Url::to(['calendar/e'], true);
} elseif ($recipient->role === Rbac::ROLE_COACH) {
    $calendarLink = Url::to(['calendar/c'], true);
} else {
    $calendarLink = Url::to(['calendar/m'], true);
}

?>
<?= $this->render('@common/mail/layouts/_header') ?>
<tr>
    <td align="left" valign="middle" style="padding-top:24px;">
            <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
              Здравствуйте, <?= Html::encode($recipient->first_name) ?>
            </span>
    </td>
</tr>


<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
       Ваша сессия с <?= Html::encode($sender->fullName) ?> на <?= Yii::$app->formatter->asDatetime($session->start_at_tc, 'php:d.m.Y') ?> в <?= Yii::$app->formatter->asDatetime($session->start_at_tc, 'php:H:i') ?> (GMT +3) подтверждена.
        </span>
    </td>
</tr>
<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
    Вы можете перенести или отменить сессию не позднее чем за 8 часов до ее начала в разделе  <?= Html::a('календарь', $calendarLink) ?>.
        </span>
    </td>
</tr>
<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
    До встречи на сессии!
        </span>
    </td>
</tr>

<?= $this->render('@common/mail/layouts/_footer') ?>
