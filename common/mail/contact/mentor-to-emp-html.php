<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $userTo User */
/* @var $userFrom User */
/* @var $body string */

$checkMentor = $userFrom->role === Rbac::ROLE_MENTOR;
$profileLink = Url::to(['profile/' . $userFrom->user_uuid], true);
if ($userFrom->role === Rbac::ROLE_MENTOR) {
    $contactLink = Url::to(['/mentors'], true);
} elseif ($userFrom->role === Rbac::ROLE_COACH) {
    $contactLink = Url::to(['/coaches'], true);
} else {
    $contactLink = Url::to(['/staff'], true);
}

?>

<?= $this->render('@common/mail/layouts/_header') ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте, <?= Html::encode($userTo->first_name) ?>
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
                    У Вас новое сообщение от <?= Html::a(Html::encode($userFrom->fullName), $profileLink) ?>:
	</span></td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
                    “<?= nl2br(Html::encode($body)) ?>”
	</span></td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
                    Для ответа перейдите по <?= Html::a('ссылке', $contactLink) ?>.
	</span></td>
    </tr>

<?= $this->render('@common/mail/layouts/_footer') ?>
