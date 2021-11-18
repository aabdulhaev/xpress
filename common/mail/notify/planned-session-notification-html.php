<?php
/**
 * @var $recipient User
 * @var $coach User
 * @var $training TrainingSession
 */

use common\models\TrainingSession;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$hours = strtotime($training->start_at_tc) - time() < 24 * 60 * 60 ? '24 часа' : '48 часов';

$profileLink = Url::to(['profile/' . $coach->user_uuid], true);

?>
<?=$this->render('@common/mail/layouts/_header')?>
<tr>
    <td align="left" valign="middle" style="padding-top:24px;">
        <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
         Здравствуйте, <?= Html::encode($recipient->first_name) ?>
        </span>
    </td>
</tr>
<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
        Ваша сессия с <?= Html::a($coach->fullName, $profileLink) ?> на платформе <?= Yii::$app->name ?> начнётся через <?= $hours ?>.
        <br>
        А пока - проверьте наличие доступа к камере и микрофону в настройках Вашего браузера.
        </span>
    </td>
</tr>
<?=$this->render('@common/mail/layouts/_footer')?>

