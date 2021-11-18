<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */

$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
?>
Здравствуйте, <?= Html::encode($employee->first_name) ?>

<?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> приостанавливает сессии с Вами<?=(!empty($comment)) ? ":":"."?>

<?php if (!empty($comment)): ?>
    "<?= nl2br(Html::encode($comment)) ?>"
<?php endif; ?>

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
