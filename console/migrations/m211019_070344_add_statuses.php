<?php

use yii\db\Migration;

/**
 * Class m211019_070344_add_statuses
 */
class m211019_070344_add_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('competence', 'status', $this->integer()->defaultValue(\common\models\Competence::STATUS_ACTIVE));
        $this->addColumn('notification', 'status', $this->integer()->defaultValue(\common\models\Notification::STATUS_ACTIVE));
        $this->addColumn('program', 'status', $this->integer()->defaultValue(\common\models\Program::STATUS_ACTIVE));
        $this->addColumn('session_rating', 'status', $this->integer()->defaultValue(\common\models\SessionRating::STATUS_ACTIVE));
        $this->addColumn('subject', 'status', $this->integer()->defaultValue(\common\models\Subject::STATUS_ACTIVE));
        $this->addColumn('tariff_plan', 'status', $this->integer()->defaultValue(\common\models\TariffPlan::STATUS_ACTIVE));
        $this->addColumn('user_competence', 'status', $this->integer()->defaultValue(\common\models\UserCompetence::STATUS_ACTIVE));
        $this->addColumn('user_competency_profile', 'status', $this->integer()->defaultValue(\common\models\UserCompetencyProfile::STATUS_ACTIVE));
        $this->addColumn('user_program', 'status', $this->integer()->defaultValue(\common\models\UserProgram::STATUS_ACTIVE));
        $this->addColumn('user_stat', 'status', $this->integer()->defaultValue(\common\models\UserStat::STATUS_ACTIVE));
        $this->addColumn('user_subject', 'status', $this->integer()->defaultValue(\common\models\UserSubject::STATUS_ACTIVE));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('competence', 'status');
        $this->dropColumn('notification', 'status');
        $this->dropColumn('program', 'status');
        $this->dropColumn('session_rating', 'status');
        $this->dropColumn('subject', 'status');
        $this->dropColumn('tariff_plan', 'status');
        $this->dropColumn('user_competence', 'status');
        $this->dropColumn('user_competency_profile', 'status');
        $this->dropColumn('user_program', 'status');
        $this->dropColumn('user_stat', 'status');
        $this->dropColumn('user_subject', 'status');
    }
}
