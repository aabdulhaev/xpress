<?php
/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */

/* @var $dateStart string */

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$profileLink = Url::to(['/profile/' . $employee->user_uuid], true);
$calendarLink = Url::to(['/calendar/c'], true);
?>
<?= $this->render('@common/mail/layouts/_header') ?>
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
        У Вас новый запрос на проведение сессии <?= $dateStart ?> (GMT +3) от
        <?= Html::a(Html::encode($employee->fullName), $profileLink) ?>.
       </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
                Перейдите в <?= Html::a('календарь', $calendarLink) ?>
                для подтверждения или предложите альтернативный слот.
           </span>
        </td>
    </tr>


<?= $this->render('@common/mail/layouts/_footer') ?>
