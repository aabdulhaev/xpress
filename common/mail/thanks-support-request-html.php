<?php
/**
 * @var $sender \common\models\User
 * @var $text string
 */
?>
<?=$this->render('@common/mail/layouts/_header')?>
<tr>
    <td align="left" valign="middle" style="padding-top:24px;" >
        <span class="title" style="font-weight:600;font-size:30px;line-height:36px;" >Спасибо за обращение!</span>
    </td>
</tr>

<tr>
    <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
            Мы получили Ваш запрос в поддержку, и сотрудник из нашей команды свяжется с Вами в ближайшее время!
            </span>
    </td>
</tr>

<?=$this->render('@common/mail/layouts/_footer',[
    'text'=>'Мы получили Ваш запрос в поддержку, и сотрудник из нашей команды свяжется с Вами в ближайшее время!'
])?>
