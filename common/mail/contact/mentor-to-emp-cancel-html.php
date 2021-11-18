<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */

$profileLink = Url::to(['profile/' . $mentor->user_uuid], true);
$checkMentor = $mentor->role === Rbac::ROLE_MENTOR;
$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'], true);
$roleName = $checkMentor ? 'ментора' : 'коуча';
?>
<?= $this->render('@common/mail/layouts/_header') ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                 Здравствуйте, <?= Html::encode($employee->first_name) ?>
                </span>
        </td>
    </tr>


    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
    К сожалению, <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> не сможет принять Ваш запрос.
	        </span>
        </td>
    </tr>

<?php if (!empty($comment)): ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
    "<?= nl2br(Html::encode($comment)) ?>"
            </span>
        </td>
    </tr>
<?php endif; ?>

    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
 Выберите нового <?= $roleName ?> из предложенного <?= Html::a('списка', $contactLink) ?>.
	</span></td>
    </tr>


<?= $this->render('@common/mail/layouts/_footer') ?>
