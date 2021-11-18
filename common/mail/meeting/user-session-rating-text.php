<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 * @var $started_at string
 * @var $subjects  array
 * @var $rate  integer
 */

use common\access\Rbac;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$roles = [
    Rbac::ROLE_EMP=>'сотрудником',
    Rbac::ROLE_COACH=>'коучем',
    Rbac::ROLE_MENTOR=>'ментором'
];
?>

    Здравствуйте!

<?= $sender->getRoleLabel() ?> <?= Html::encode($sender->fullName) ?> оценил сессию <?= Yii::$app->formatter->asDatetime($session->start_at_tc, 'php:d-m-Y H:i') ?> (GMT +3)
    с <?= ArrayHelper::getValue($roles, $recipient->role) ?> <?= Html::encode($recipient->fullName) ?> в «<?=Html::encode($rate) ?>»


<?php if (!empty($comment)): ?>
    «<?= nl2br(trim(Html::encode($comment))) ?>»
<?php endif; ?>

<?php
if (!empty($subjects) && is_array($subjects)) {
    echo "и выбрал цели: " . implode(", ", ArrayHelper::getColumn($subjects, 'title'));
}
?>
