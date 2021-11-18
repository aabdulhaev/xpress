<?php

/* @var $this yii\web\View */

/* @var $user User */
/* @var $pwd string */

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$confirmLink = Url::to([Yii::$app->params['confirmEmailFrontLink'],'token'=> $user->verification_token],true);
$privacyPolicyLink = 'https://www.xpress.loc/privacy_policy';
?>
Здравствуйте <?= $user->first_name ?>,

Мы рады приветствовать Вас на платформе <?=Yii::$app->name?>.

Для завершения регистрации перейдите, пожалуйста, по ссылке:

<?= Html::a('Подтвердить', $confirmLink) ?>

Или нажмите на кнопку ниже:

<?= Html::a('ПОДТВЕРДИТЬ РЕГИСТРАЦИЮ', $confirmLink) ?>

Для входа в личный кабинет используйте в качестве логина свой email и пароль:  <?= $pwd ?>
Вы сможете изменить пароль в личном кабинете после первого входа.

Подтверждая регистрацию, вы соглашаетесь с <?= Html::a('условиями политики конфиденциальности',$privacyPolicyLink)?> <?=Yii::$app->name?>.

© <?= Yii::$app->name ?>. Все права защищены.

Если у вас возникли вопросы, напишите нам на <?= \Yii::$app->params['supportEmail'] ?>. или позвоните:
<?= \Yii::$app->params['senderPhone'] ?>
