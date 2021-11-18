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

<?=$this->render('@common/mail/layouts/_header')?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте, <?= Html::encode($mentor->first_name) ?>
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
     <?= Html::a(Html::encode($employee->fullName), $profileLink) ?> приостанавливает сессии с Вами<?= (!empty($comment)) ? ":" : "." ?>
	</span></td>
    </tr>
<?php if (!empty($comment)): ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
    <?= nl2br(Html::decode($comment)) ?>
	</span></td>
    </tr>
<?php endif; ?>
<?=$this->render('@common/mail/layouts/_footer')?>
