<?php

use common\access\Rbac;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $employee User */
/* @var $mentorsRole string */

$checkMentor = $mentorsRole === Rbac::ROLE_MENTOR;

$contactLink = Url::to([$checkMentor ? '/mentors' : '/coaches'],true);

$role = $checkMentor ? ['менторы','ментора','"Менторы"','Ментор'] : ['коучи','коуча','"Коучи"','Коуч'];
?>

Здравствуйте <?= Html::encode($employee->fullName) ?>,
Вам назначены <?= $role[0] ?>.
Для выбора <?= $role[1] ?> перейдите в раздел <?= Html::a($role[2], $contactLink) ?>, изучите портфолио, специализацию и сделайте свой выбор – откликнетесь и расскажите немного о себе и своих целях от взаимодействия. <?= $role[3] ?> ответит на Ваш отклик в течение 24 часов, и Вы сможете назначить первую сессию.

Желаем Вам продуктивных сессий и достижения целей!
