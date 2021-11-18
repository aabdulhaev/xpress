<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $code string
 */

?>
<div class="password-reset">
  <p>Здравствуйте!</p>

  <p>Вы получили это письмо, потому что кто-то запросил сброс пароля для вашего аккаунта.</p>

  <p>
    Ваш код для сброса пароля:
    <code><?= Html::encode($code) ?></code>
  </p>

  <p>Если вы не запрашивали сброс пароля, то просто проигнорируйте это письмо.</p>
</div>
