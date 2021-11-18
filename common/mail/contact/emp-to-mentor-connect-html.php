<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */

$profileLink = Url::to(['profile/' . $employee->user_uuid], true);
$confirmLink = Url::to([Yii::$app->params['confirmRelFrontLink'], 'token' => $employee->user_uuid], true);
$declineLink = Url::to([Yii::$app->params['declineRelFrontLink'], 'token' => $employee->user_uuid], true);
?>

<?=$this->render('@common/mail/layouts/_header')?>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте <?= Html::encode($mentor->first_name) ?>,
                </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                             style="font-size:16px;color:#546B82;">
            <p>У Вас новый запрос на начало сотрудничества от <?= Html::a($employee->fullName, $profileLink) ?>.</p>
        </span></td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                             style="font-size:16px;color:#546B82;">
            <p>"<?= nl2br(Html::encode($comment)) ?>"</p>
        </span></td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                             style="font-size:16px;color:#546B82;">
            <p><?= Html::a('Примите', $confirmLink) ?> запрос и запланируйте первую сессию или <?= Html::a('отклоните', $declineLink) ?> с указанием причины.</p>
        </span></td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                             style="font-size:16px;color:#546B82;">
           <p>Желаем Вам плодотворного сотрудничества!</p>
        </span></td>
        </tr>
<?=$this->render('@common/mail/layouts/_footer')?>
