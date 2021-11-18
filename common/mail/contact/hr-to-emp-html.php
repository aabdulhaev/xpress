<?php

use common\models\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $userTo User */
/* @var $userFrom User */
/* @var $body string */


?>

<?= $this->render('@common/mail/layouts/_header') ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте <?= Html::encode($userTo->first_name) ?>,
                </span>
        </td>
    </tr>

    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
         Пользователь <?= $userFrom->fullName ?> оставил Вам сообщение:
	</span></td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
                 <?= nl2br(Html::encode($body)) ?>
	</span></td>
    </tr>


<?= $this->render('@common/mail/layouts/_footer') ?>