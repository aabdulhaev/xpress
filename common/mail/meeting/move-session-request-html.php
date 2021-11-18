<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 * @var $comment string
 * @var $fromDateTime string
 * @var $toDateTime string
 */

use common\access\Rbac;
use yii\helpers\Html;
use yii\helpers\Url;

$formatter = Yii::$app->formatter;

$calendarLink = Url::to(['calendar/m'],true);
if($recipient->role === Rbac::ROLE_EMP){
    $calendarLink = Url::to(['calendar/e'],true);
} elseif ($recipient->role === Rbac::ROLE_COACH){
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




<tr><td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
    <?= Html::encode($sender->fullName)?> просит перенести сессию c
        <?=$formatter->asDatetime($fromDateTime,'php:d.m.Y')?> в <?=$formatter->asDatetime($fromDateTime,'php:H:i')?> (GMT +3) на
        <?=$formatter->asDatetime($toDateTime,'php:d.m.Y')?> в <?=$formatter->asDatetime($toDateTime,'php:H:i')?> (GMT +3).
        </span>
    </td>
</tr>
<?php if (!empty($comment)): ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
             "<?= nl2br(Html::encode($comment,false)) ?>"
            </span>
        </td>
    </tr>
<?php endif; ?>


<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
    Перейдите в <?= Html::a('календарь', $calendarLink) ?> для подтверждения или предложите альтернативный слот.
        </span>
    </td>
</tr>

<?=$this->render('@common/mail/layouts/_footer')?>



