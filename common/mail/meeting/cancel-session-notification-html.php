<?php
/**
 * @var $session TrainingSession
 * @var $sender User
 * @var $recipient User
 * @var $comment string
 */

use common\access\Rbac;
use common\models\TrainingSession;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$formatter = Yii::$app->formatter;

$calendarLink = Url::to(['calendar/m'],true);
if ($recipient->role === Rbac::ROLE_EMP) {
    $calendarLink = Url::to(['calendar/e'],true);
} elseif ($recipient->role === Rbac::ROLE_COACH) {
    $calendarLink = Url::to(['calendar/c'],true);
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
    <td align="left" valign="middle" style="padding-top:16px;"><span class="text" style="font-size:16px;color:#546B82;">
        <?= Html::encode($sender->fullName) ?> отменяет сессию
        <?= $formatter->asDatetime($session->start_at_tc, 'php:d.m.Y') ?> в
        <?= $formatter->asDatetime($session->start_at_tc, 'php:H:i') ?> (GMT +3).
    </span></td>
</tr>
<?php if (!empty($comment)): ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
                "<?= nl2br( Html::encode($comment,false)) ?>"
            </span>
        </td>
    </tr>
<?php endif; ?>
<?=$this->render('@common/mail/layouts/_footer')?>

