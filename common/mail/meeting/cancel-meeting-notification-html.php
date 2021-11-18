<?php

use yii\helpers\Html;

/**
 * @var $meeting \common\models\Meeting
 */

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
            Хотим сообщить, что вебинар '<?= Html::encode($meeting->title) ?>', запланированный на <?= $meeting->formatStartDate() ?> в <?= $meeting->formatStartTime() ?> (GMT +3), отменен по техническим причинам.
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
        <span class="text" style="font-size:16px;color:#546B82;" >
            Приносим свои извинения!
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
