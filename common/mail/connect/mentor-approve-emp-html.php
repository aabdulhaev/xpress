<?php
/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */

/* @var $comment string */


use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;
$profileLink = Url::to(['profile/' . $mentor->user_uuid], true);
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'], true);
$calendarLink = Url::to(['calendar/e'], true);
?>
<?= $this->render('@common/mail/layouts/_header') ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте, <?= Html::encode($employee->first_name) ?>,
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
            <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> принимает Ваш запрос.
            Вы можете <?= Html::a('связаться', $contactLink) ?> или назначить первую сессию в
            <?= Html::a('календаре', $calendarLink) ?>.
    </span></td>
    </tr>

    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
                    Желаем Вам плодотворного сотрудничества!
    </span></td>
    </tr>
<?= $this->render('@common/mail/layouts/_footer') ?>
