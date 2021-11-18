<?php

/* @var $this yii\web\View */
/* @var $user \common\models\User */

use yii\helpers\Url;

$resetLink = Url::to(['/','token'=> $user->password_reset_token],true);

?>
Здравствуйте <?= $user->first_name ?>,

Вы забыли свой пароль?

Вы отправили запрос на изменение пароля. Вы можете изменить пароль нажав на кнопку ниже:

ВОССТАНОВИТЬ ПАРОЛЬ <?= $resetLink ?>

Если Вы не совершали данное действие, то игнорируйте данное письмо.

    © <?= Yii::$app->name ?>. Все права защищены.

    Если у вас возникли вопросы, напишите нам на <?= \Yii::$app->params['supportEmail'] ?>. или позвоните:
<?= \Yii::$app->params['senderPhone'] ?>
