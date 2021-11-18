<?php
/**
 * @var $sender \common\models\User
 * @var $text string
 */

use yii\helpers\Html;

?>
В службу поддержки обратился <?=$sender->getRoleLabel() ?> <?= Html::encode($sender->fullName)?>, <?=$sender->email ?>.


Текст сообщения:


<?=nl2br(Html::encode($text))?>


