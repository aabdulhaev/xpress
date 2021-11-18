<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 * @var $comment string
 * @var $fromDateTime string
 * @var $toDateTime string
 */

use common\access\Rbac;
use yii\helpers\Html;
use yii\helpers\Url;

$formatter = Yii::$app->formatter;
if($recipient->role === Rbac::ROLE_EMP){
    $calendarLink = Url::to(['calendar/e'],true);
}elseif ($recipient->role === Rbac::ROLE_COACH){
    $calendarLink = Url::to(['calendar/c'],true);
}else{
    $calendarLink = Url::to(['calendar/m'],true);
}

?>
Здравствуйте, <?= Html::encode($recipient->first_name) ?>

<?= Html::encode($sender->fullName)?> просит перенести сессию c
<?=$formatter->asDatetime($fromDateTime,'php:d.m.Y')?> в <?=$formatter->asDatetime($fromDateTime,'php:H:i')?> (GMT +3) на
<?=$formatter->asDatetime($toDateTime,'php:d.m.Y')?> в <?=$formatter->asDatetime($toDateTime,'php:H:i')?> (GMT +3).

<?php if (!empty($comment)): ?>
    "<?= nl2br(Html::encode($comment,false)) ?>"
<?php endif; ?>

Перейдите в календарь (<?=$calendarLink?>) для подтверждения или предложите альтернативный слот.

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>
