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
<?=$this->render('@common/mail/layouts/_header')?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                  Здравствуйте <?= Html::encode($employee->fullName) ?>,
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;"><span class="text"
                                                                         style="font-size:16px;color:#546B82;">
                Вам назначены <?= $role[0] ?>.
            </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
Для выбора <?= $role[1] ?> перейдите в раздел <?= Html::a($role[2], $contactLink) ?>,
 изучите портфолио, специализацию и сделайте свой выбор – откликнетесь и
 расскажите немного о себе и своих целях от взаимодействия.
 <?= $role[3] ?> ответит на Ваш отклик в течение 24 часов, и Вы сможете назначить первую сессию.
            </span>
        </td>
    </tr>
<?=$this->render('@common/mail/layouts/_footer',['text'=>"Желаем Вам продуктивных сессий и достижения целей!"])?>
