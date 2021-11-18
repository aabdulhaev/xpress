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
    <tr><td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
                Вы приостановили сессии с <?= Html::a(Html::encode($employee->fullName), $profileLink) ?>. Если Вы сделали это по ошибке, свяжитесь со службой поддержки - <?=\Yii::$app->params['supportEmail']?>
            </span>
        </td>
    </tr>


<?=$this->render('@common/mail/layouts/_footer')?>
