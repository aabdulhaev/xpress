<?php

use common\models\User;
use Ramsey\Uuid\Uuid;
use yii\db\Migration;

/**
 * Class m210423_125254_seed_subjects_and_competencies
 */
class m210423_125254_seed_subjects_and_competencies extends Migration
{
    private $subjectTbNm = '{{%subject}}';
    private $competenceTbNm = '{{%competence}}';
    private $userCompetenceTbNm = '{{%user_competence}}';
    private $userSubjectTbNm = '{{%competence}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $subjects = [
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Бизнес-мышление',
                'description' => 'Бизнес-мышление',
                'img_name' => 'business_mind',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Готовность к изменениям',
                'description' => 'Готовность к изменениям',
                'img_name' => 'changing',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Клиентоориентированность',
                'description' => 'Клиентоориентированность',
                'img_name' => 'clients',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Коммуникация',
                'description' => 'Коммуникация',
                'img_name' => 'communication',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Корпоративная культура',
                'description' => 'Корпоративная культура',
                'img_name' => 'corporate_culture',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Лидерство',
                'description' => 'Лидерство',
                'img_name' => 'leadership',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Мотивация команды',
                'description' => 'Мотивация команды',
                'img_name' => 'team_motivation',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Организация работы',
                'description' => 'Организация работы',
                'img_name' => 'process',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Ориентация на результат',
                'description' => 'Ориентация на результат',
                'img_name' => 'result',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Ответственность',
                'description' => 'Ответственность',
                'img_name' => 'responsibility',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Планирование',
                'description' => 'Планирование',
                'img_name' => 'planning',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Принятие решений',
                'description' => 'Принятие решений',
                'img_name' => 'decision-making',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Проактивность',
                'description' => 'Проактивность',
                'img_name' => 'proactivity',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Профессиональные знания',
                'description' => 'Профессиональные знания',
                'img_name' => 'knowledge',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Работа в команде',
                'description' => 'Работа в команде',
                'img_name' => 'team',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Развитие команды',
                'description' => 'Развитие команды',
                'img_name' => 'team_development',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Саморазвитие',
                'description' => 'Саморазвитие',
                'img_name' => 'upgrade',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Сотрудничество',
                'description' => 'Сотрудничество',
                'img_name' => 'collaboration',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Стратегическое мышление',
                'description' => 'Стратегическое мышление',
                'img_name' => 'strategic-mind',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Стрессоустойчивость',
                'description' => 'Стрессоустойчивость',
                'img_name' => 'strong',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Управление командой',
                'description' => 'Управление командой',
                'img_name' => 'team_management',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Этика и моральные ценности',
                'description' => 'Этика и моральные ценности',
                'img_name' => 'ethics',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ]
        ];

        $this->batchInsert(
            $this->subjectTbNm,
            [
                'subject_uuid',
                'title',
                'description',
                'img_name',
                'created_at',
                'created_by'
            ],
            $subjects
        );

        $competencies = [
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Личное развитие',
                'description' => 'Личное развитие',
                'img_name' => 'personal_development',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Эффективность',
                'description' => 'Эффективность',
                'img_name' => 'efficiency',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Постановка целей',
                'description' => 'Постановка целей',
                'img_name' => 'goals',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Осознанность',
                'description' => 'Осознанность',
                'img_name' => 'mindfulness',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Здоровье и благополучие',
                'description' => 'Здоровье и благополучие',
                'img_name' => 'health',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Лидерство',
                'description' => 'Лидерство',
                'img_name' => 'leadership',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Управление командой',
                'description' => 'Управление командой',
                'img_name' => 'team_management',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Развивающий стиль руководства',
                'description' => 'Развивающий стиль руководства',
                'img_name' => 'up_management',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Коммуникация',
                'description' => 'Коммуникация',
                'img_name' => 'communication',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Работа с конфликтами',
                'description' => 'Работа с конфликтами',
                'img_name' => 'working_with_conflicts',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'competence_uuid' => Uuid::uuid6()->toString(),
                'title' => 'Управление эмоциями',
                'description' => 'Управление эмоциями',
                'img_name' => 'emotions',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ]
        ];

        $this->batchInsert(
            $this->competenceTbNm,
            [
                'competence_uuid',
                'title',
                'description',
                'img_name',
                'created_at',
                'created_by'
            ],
            $competencies
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('SET CONSTRAINTS ALL DEFERRED');
        $this->truncateTable($this->userSubjectTbNm);
        $this->dropIndex('subject_subject_uuid_key', $this->subjectTbNm);
        $this->truncateTable($this->subjectTbNm);
        $this->createIndex(
            'subject_subject_uuid_key',
            $this->subjectTbNm,
            'subject_uuid',
            true
        );

        $this->truncateTable($this->userCompetenceTbNm);
        $this->dropIndex('competence_competence_uuid_key', $this->competenceTbNm);
        $this->truncateTable($this->competenceTbNm);
        $this->createIndex(
            'competence_competence_uuid_key',
            $this->competenceTbNm,
            'competence_uuid',
            true
        );
        $this->execute('SET CONSTRAINTS ALL IMMEDIATE');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_125254_seed_subjects_and_competencies cannot be reverted.\n";

        return false;
    }
    */
}
