<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 * @var $token string
 */

$link = $meeting->prepareJoinLink($token);
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
            Добрый день!
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
            Напоминаем Вам, что завтра пройдет вебинар '<?= Html::encode($meeting->title) ?>'.
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
            <?= Html::encode($meeting->description) ?>
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
            Ждем Вас <?= $meeting->formatStartDate() ?> в <?= $meeting->formatStartTime() ?> (GMT +3)
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
            Для подключения к видео-конференции перейдите, пожалуйста, по ссылке.
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;">
                <div><!--[if mso]>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                                 href="<?= $link ?>"  arcsize="50%" stroke="f"
                                 fillcolor="#00AAFF" style="height:52px;v-text-anchor:middle;width:337px;" >
                        <w:anchorlock/>
                        <center>
                    <![endif]-->
                    <a href="<?= $link ?>"
                       style="background-color:#00AAFF;border-radius:26px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:14px;font-weight:bold;line-height:52px;text-align:center;text-decoration:none;width:292px;-webkit-text-size-adjust:none;text-transform:uppercase;">ПОДКЛЮЧИТЬСЯ
                    </a>
                    <!--[if mso]>
                    </center>
                    </v:roundrect>
                    <![endif]--></div>
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
                    команда поддержки пользователей <?=Yii::$app->name?>.
                </p>
            </td>
        </tr>
    </table>
</center>
