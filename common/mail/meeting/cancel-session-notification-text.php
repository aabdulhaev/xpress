<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 * @var $comment string
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

<?= Html::encode($sender->fullName)?> отменяет сессию <?=$formatter->asDatetime($session->start_at_tc,'php:d.m.Y')?> в <?=$formatter->asDatetime($session->start_at_tc,'php:H:i')?> (GMT +3).

<?php if (!empty($comment)): ?>
    "<?= nl2br(Html::encode($comment,false)) ?>"
<?php endif; ?>

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>
