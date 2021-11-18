<?php
/**
 * @var $sender \common\models\User
 * @var $text string
 */

use yii\helpers\Html;

?>
<?=$this->render('@common/mail/layouts/_header')?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  В службу поддержки обратился <?=$sender->getRoleLabel() ?>, <strong><?= Html::encode($sender->fullName)?></strong>.
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
				<span class="text" style="font-size:16px;color:#546B82;">
            <?= Html::a($sender->email,'mailto:'.$sender->email) ?>
				</span>
        </td>
    </tr>

    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
				<span class="text" style="font-size:16px;color:#546B82;">
                    Текст сообщения:
				</span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
				<span class="text" style="font-size:16px;color:#546B82;">
                    <?=nl2br(Html::encode($text))?>
				</span>
        </td>
    </tr>
</table>
</center>
