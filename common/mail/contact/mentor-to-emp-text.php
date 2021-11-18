<?php

/* @var $this yii\web\View */

/* @var $userTo User */
/* @var $userFrom User */
/* @var $body string */

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$checkMentor = $userFrom->role === Rbac::ROLE_MENTOR;
$profileLink = Url::to(['profile/'.$userFrom->user_uuid], true);
if($userFrom->role === Rbac::ROLE_MENTOR){
    $contactLink = Url::to(['/mentors'],true);
}elseif ($userFrom->role === Rbac::ROLE_COACH){
    $contactLink = Url::to(['/coaches'],true);
}else{
    $contactLink = Url::to(['/staff'],true);
}

?>
Здравствуйте, <?= Html::encode($userTo->first_name) ?>

У Вас новое сообщение от <?= Html::a(Html::encode($userFrom->fullName), $profileLink) ?>:

“<?=nl2br(Html::encode($body)) ?>”

Для ответа перейдите по <?= Html::a('ссылке', $contactLink) ?>.

С Уважением,
команда поддержки пользователей <?= Yii::$app->name ?>
