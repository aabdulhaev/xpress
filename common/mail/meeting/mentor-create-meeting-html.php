<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $mentor User
 * @var $employee User
 * @var $role string
 */
$link = Yii::$app->params['frontHost'] . '/' . Yii::$app->params['profileFrontLink'];
$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$calendarLink = Url::to(['calendar/e'],true);
?>
<center>
    <table class="main-table" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="max-width:600px;width:100%;background-color:#FFFFFF;padding-top:0px;padding-left:24px;padding-bottom:0px;padding-right:24px;border-radius:7px;font-family:Roboto, Arial, Tahoma, sans-serif;color:#000000;" >
        <tr>
            <td align="center" valign="middle" style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#E5E5E5;height:80px;" >
                <img src="<?= \Yii::$app->params['logoPath'] ?>" alt="Logo" border="0" width="162" height="21" style="display:block;" />
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;" >
        <span class="title" style="font-weight:600;font-size:30px;line-height:36px;" >
            Здравствуйте <?= Html::encode($employee->first_name) ?>,
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
          <?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> ожидает Вас на сессии.
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
          Войдите в конференцию с главной страницы своего <?= Html::a('профиля', $link) ?> или из раздела <?= Html::a('календарь', $calendarLink) ?>.
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
         Перед началом сессии убедитесь, что в настройках Вашего браузера предоставлен доступ к микрофону и камере.
        </span>
            </td>
        </tr>


        <tr>
            <td align="left" valign="middle"
                style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#E5E5E5;padding-top:16px;padding-bottom:24px;">

                <p class="text" style="font-size:16px;color:#546B82;">
                    С Уважением,
                    команда поддержки пользователей <?=Yii::$app->name?>
                </p>
            </td>
        </tr>
        <tr>
            <td align="center" valign="middle" style="padding-top:24px;">
                <span class="text-small" style="font-size:14px;color:#546B82;"> © <?= Yii::$app->name ?>. Все права защищены.</span>
            </td>
        </tr>
        <tr>
            <td align="center" valign="middle" style="padding-bottom:24px;padding-top:12px;" >
    <span class="text-small" style="font-size:14px;color:#546B82;" >
      Если у вас возникли вопросы, напишите нам на
      <a class="link" href="mailto:<?= \Yii::$app->params['supportEmail'] ?>" target="_blank" style="line-height:24px;color:#00AAFF !important;" ><?= \Yii::$app->params['supportEmail'] ?>.</a>
      или позвоните:
      <br>
      <?= \Yii::$app->params['senderPhone'] ?>
    </span>
            </td>
        </tr>
    </table>
</center>
