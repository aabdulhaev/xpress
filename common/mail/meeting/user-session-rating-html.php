<?php
/**
 * @var $session \common\models\TrainingSession
 * @var $sender \common\models\User
 * @var $recipient \common\models\User
 * @var $started_at string
 * @var $subjects  array
 * @var $rate  integer
 */

use common\access\Rbac;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$roles = [
    Rbac::ROLE_EMP => 'сотрудником',
    Rbac::ROLE_COACH => 'коучем',
    Rbac::ROLE_MENTOR => 'ментором'
];
?>

<?= $this->render('@common/mail/layouts/_header') ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:24px;">
                <span class="title" style="font-weight:600;font-size:30px;line-height:36px;">
                 Здравствуйте!
                </span>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
            <span class="text" style="font-size:16px;color:#546B82;">
                            <?= $sender->getRoleLabel() ?> <?= Html::encode($sender->fullName) ?> оценил сессию <?= Yii::$app->formatter->asDatetime($session->start_at_tc, 'php:d-m-Y H:i') ?> (GMT +3)
            с <?= ArrayHelper::getValue($roles, $recipient->role) ?> <?= Html::encode($recipient->fullName) ?> в «<?= Html::encode($rate) ?>»
            </span>
        </td>
    </tr>

<?php if (!empty($comment)): ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
				<span class="text" style="font-size:16px;color:#546B82;">
				 «<?= nl2br(trim(Html::encode($comment))) ?>»
				</span>
        </td>
    </tr>
<?php endif; ?>
    <tr>
        <td align="left" valign="middle" style="padding-top:16px;">
				<span class="text" style="font-size:16px;color:#546B82;">
				<?php
                if (!empty($subjects) && is_array($subjects)) {
                    echo "и выбрал цели: " . implode(", ", ArrayHelper::getColumn($subjects, 'title'));
                }
                ?>
				</span>
        </td>
    </tr>

<?= $this->render('@common/mail/layouts/_footer') ?>
<?php
