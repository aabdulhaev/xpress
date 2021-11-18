<?php
/**
 * @var $this \yii\web\View
 * @var $text string
 */

if (empty($text)){
    $text = "С Уважением,
            команда поддержки пользователей ". Yii::$app->name;
}

?>
<tr>
    <td align="left" valign="middle"
        style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#E5E5E5;padding-top:16px;padding-bottom:24px;">
        <p class="text" style="font-size:16px;color:#546B82;">
            <?= $text ?>
        </p>
    </td>
</tr>
<tr>
    <td align="center" valign="middle" style="padding-top:24px;">
        <span class="text-small"
              style="font-size:14px;color:#546B82;"> © <?= Yii::$app->name ?>. Все права защищены.</span>
    </td>
</tr>
<tr>
    <td align="center" valign="middle" style="padding-bottom:24px;padding-top:12px;">
	<span class="text-small" style="font-size:14px;color:#546B82;">
	  Если у вас возникли вопросы, напишите нам на
	  <a class="link" href="mailto:<?= \Yii::$app->params['supportEmail'] ?>" target="_blank"
         style="line-height:24px;color:#00AAFF !important;"><?= \Yii::$app->params['supportEmail'] ?>.</a>
	  или позвоните:
	  <br>
	  <?= \Yii::$app->params['senderPhone'] ?>
	</span>
    </td>
</tr>
</table>
</center>
