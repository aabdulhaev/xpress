<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */


$profileLink = Url::to(['profile/'.$employee->user_uuid], true);
?>
Здравствуйте, <?= Html::encode($mentor->first_name) ?>

<?= Html::a(Html::encode($employee->fullName), $profileLink) ?> приостанавливает сессии с Вами<?=(!empty($comment)) ? ":":"."?>

<?php if (!empty($comment)): ?>
    <p><?= nl2br(Html::encode($comment)) ?></p>
<?php endif; ?>

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
