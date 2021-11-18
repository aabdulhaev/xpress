<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user \common\models\User */


$resetLink = Url::to(['/', 'token'=> $user->password_reset_token],true);

?>
<?=$this->render('@common/mail/layouts/_header')?>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;" >
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;" >Вы забыли свой пароль?</span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;" >
                <span class="text" style="font-size:16px;color:#546B82;">
                  Вы отправили запрос на изменение пароля. Вы можете изменить пароль нажав на кнопку ниже:
                </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;" >
                <div><!--[if mso]>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                                 href="<?=$resetLink?>"  arcsize="50%"
                                 strokecolor="#00AAFF" fillcolor="#FFFFFF" style="height:48px;v-text-anchor:middle;width:292px;" >
                        <w:anchorlock/>
                        <center style="color:#00AAFF;font-family:sans-serif;font-size:13px;font-weight:bold;" >ВОССТАНОВИТЬ
                            ПАРОЛЬ
                        </center>
                    </v:roundrect>
                    <![endif]--><a href="<?=$resetLink?>" style="background-color:#FFFFFF;border-width:2px;border-style:solid;border-color:#00AAFF;border-radius:26px;color:#00AAFF;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:48px;text-align:center;text-decoration:none;width:292px;-webkit-text-size-adjust:none;mso-hide:all;text-transform:uppercase;" >ВОССТАНОВИТЬ
                        ПАРОЛЬ</a></div>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
                <p class="text" style="font-size:16px;color:#546B82;" >
                    Если Вы не совершали данное действие, то игнорируйте данное письмо.
                </p>
            </td>
        </tr>
<?=$this->render('@common/mail/layouts/_footer')?>
