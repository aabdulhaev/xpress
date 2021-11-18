<?php

/* @var $this yii\web\View */

/* @var $userTo User */
/* @var $userFrom User */
/* @var $body string */

use common\models\User;

?>
Здравствуйте <?= $userTo->first_name ?>,

Пользователь <?= $userFrom->fullName ?> оставил Вам сообщение.

"<?= $body ?>"

С Уважением,

команда поддержки пользователей <?= Yii::$app->name ?>
