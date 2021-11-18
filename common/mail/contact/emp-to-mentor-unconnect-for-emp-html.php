<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */
?>
<?php
$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;
$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'],true);
$roleName = $mentor->role === Rbac::ROLE_MENTOR ? 'ментора' : 'коуча';
?>
<?=$this->render('@common/mail/layouts/_header')?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте <?= Html::encode($employee->first_name) ?>,
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
                Вы приостановили сессии с <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?>. Если Вы сделали это по ошибке, свяжитесь со службой поддержки - <?= \Yii::$app->params['supportEmail'] ?>
            </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text"
                  style="font-size:16px;color:#546B82;">Вы можете выбрать нового <?= Html::a($roleName, $contactLink) ?> для продолжения сессий на платформе.</span>
        </td>
    </tr>


<?=$this->render('@common/mail/layouts/_footer')?>
