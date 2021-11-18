<?php

use common\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $pwd string */

$confirmLink = Url::to([Yii::$app->params['confirmEmailFrontLink'], 'token' => $user->verification_token], true);
$privacyPolicyLink = 'https://www.xpress.loc/privacy_policy';
?>
<?=$this->render('@common/mail/layouts/_header')?>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;" >
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;" >Мы рады приветствовать Вас на платформе <?= Yii::$app->name ?>!</span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
          Для завершения регистрации перейдите, пожалуйста, по ссылке:
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
                <a class="link" href="<?= $confirmLink ?>" target="_blank"
                   style="line-height:24px;color:#00AAFF !important;">Подтвердить</a>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
        <span class="text" style="font-size:16px;color:#546B82;">
          Или нажмите на кнопку ниже:
        </span>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;">
                <div><!--[if mso]>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                                 href="<?= $confirmLink ?>"  arcsize="50%" stroke="f"
                                 fillcolor="#00AAFF" style="height:52px;v-text-anchor:middle;width:337px;" >
                        <w:anchorlock/>
                        <center>
                    <![endif]-->
                    <a href="<?= $confirmLink ?>"
                       style="background-color:#00AAFF;border-radius:26px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:14px;font-weight:bold;line-height:52px;text-align:center;text-decoration:none;width:292px;-webkit-text-size-adjust:none;text-transform:uppercase;">ПОДТВЕРДИТЬ
                        РЕГИСТРАЦИЮ</a>
                    <!--[if mso]>
                    </center>
                    </v:roundrect>
                    <![endif]--></div>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:24px;">
                <p class="text" style="font-size:16px;color:#546B82;">
                    Для входа в личный кабинет используйте в качестве логина свой email и пароль: <strong><?= $pwd ?></strong>
                </p>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
                <p class="text" style="font-size:16px;color:#546B82;">
                    Вы сможете изменить пароль в личном кабинете после первого входа.
                </p>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle" style="padding-top:16px;">
                <p class="text" style="font-size:16px;color:#546B82;">
                    Подтверждая регистрацию, вы соглашаетесь с <a class="link" href="<?=$privacyPolicyLink?>" target="_blank"
                                                                  style="line-height:24px;color:#00AAFF !important;">условиями
                        политики
                        конфиденциальности</a> <?= Yii::$app->name ?>.
                </p>
            </td>
        </tr>
<?=$this->render('@common/mail/layouts/_footer')?>
