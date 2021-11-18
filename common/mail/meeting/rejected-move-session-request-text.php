<?php

/**
 * @var $session TrainingSession
 * @var $sender User
 * @var $recipient User
 * @var $comment string
 * @var $fromDateTime string
 * @var $toDateTime string
 */

use common\access\Rbac;
use common\models\TrainingSession;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$formatter = Yii::$app->formatter;
if ($recipient->role === Rbac::ROLE_EMP) {
    $calendarLink = Url::to(['calendar/e'], true);
} elseif ($recipient->role === Rbac::ROLE_COACH) {
    $calendarLink = Url::to(['calendar/c'], true);
} else {
    $calendarLink = Url::to(['calendar/m'], true);
}
$profileLink = Url::to(['profile/' . $sender->user_uuid], true);
$role = $sender->isUserRoleCoach() ? 'коуч' : 'ментор';

?>
Здравствуйте, <?= Html::encode($recipient->first_name) ?>

Ваш <?= $role ?> <?=
    Html::a(Html::encode($sender->fullName), $profileLink)
?> не может провести сессию в предложенные дату и время и подтвердить перенос.

<?php if (!empty($comment)) : ?>
    "<?= nl2br(Html::encode($comment, false)) ?>"
<?php endif; ?>

Если перенос для Вас еще актуален, отмените ранее подтвержденную сессию в <?=
    Html::a('календаре', $calendarLink)
?> и выберете свободный слот коуча, отмеченный зеленым.

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
