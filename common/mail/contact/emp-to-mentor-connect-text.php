<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentor User */
/* @var $comment string */

$profileLink = Url::to(['profile/'.$employee->user_uuid], true);
$confirmLink = Url::to([Yii::$app->params['confirmRelFrontLink'],'token'=> $employee->user_uuid],true);
$declineLink = Url::to([Yii::$app->params['declineRelFrontLink'],'token'=> $employee->user_uuid],true);

?>
Здравствуйте <?= Html::encode($mentor->first_name) ?>,

У Вас новый запрос на начало сотрудничества от <?= Html::a($employee->fullName, $profileLink) ?>.

"<?= nl2br(Html::encode($comment)) ?>"

Примите (<?= $confirmLink?>) запрос и запланируйте первую сессию или отклоните (<?= $declineLink ?>) с указанием причины.

Желаем Вам плодотворного сотрудничества!


С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
