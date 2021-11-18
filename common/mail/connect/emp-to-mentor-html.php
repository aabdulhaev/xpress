<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */

$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;

$profileLink = Url::to(['profile/' . $mentor->user_uuid], true);
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'], true);
$calendarLink = Url::to(['calendar/e'], true);

$role = $checkMentor ? 'ментор' : 'коуч';
?>
<?= $this->render('@common/mail/layouts/_header') ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте <?= Html::encode($employee->fullName) ?>,
                </span>
        </td>
    </tr>

    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
              Вам назначен <?= $role ?> <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?>.
	</span></td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
          Вы можете <?= Html::a('связаться', $contactLink) ?> или назначить первую
              сессию в <?= Html::a('календаре', $calendarLink) ?>.
	</span></td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
         <p>Желаем Вам плодотворного сотрудничества!</p>
	</span></td>
    </tr>
<?= $this->render('@common/mail/layouts/_footer') ?>
