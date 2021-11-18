<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $mentor User
 * @var $employee User
 * @var $role string
 */

$link = Yii::$app->params['frontHost'] . '/' . Yii::$app->params['profileFrontLink'];
$profileLink = Url::to(['profile/'.$mentor->user_uuid], true);
$calendarLink = Url::to(['calendar/e'],true);
?>

Здравствуйте <?= Html::encode($employee->first_name) ?>

<?= Html::a(Html::encode($mentor->fullName), $profileLink) ?> ожидает Вас на сессии.

Войдите в конференцию с главной страницы своего <?= Html::a('профиля', $link) ?> или из раздела <?= Html::a('календарь', $calendarLink) ?>.

Перед началом сессии убедитесь, что в настройках Вашего браузера предоставлен доступ к микрофону и камере.

С Уважением,
команда поддержки пользователей <?=Yii::$app->name ?>
