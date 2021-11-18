<?php

declare(strict_types=1);

namespace console\controllers;

use common\access\Rbac;
use common\models\Competence;
use common\models\Subject;
use common\models\User;
use common\models\UserCompetence;
use common\models\UserSubject;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\console\Controller;

class InitController extends Controller
{

    public function actionIndex()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $admin = $auth->createRole(Rbac::ROLE_ADMIN);
        $hr = $auth->createRole(Rbac::ROLE_HR);
        $emp = $auth->createRole(Rbac::ROLE_EMP);
        $mentor = $auth->createRole(Rbac::ROLE_MENTOR);
        $coach = $auth->createRole(Rbac::ROLE_COACH);
        $moderator = $auth->createRole(Rbac::ROLE_MODERATOR);

        $auth->add($admin);
        $auth->add($hr);
        $auth->add($emp);
        $auth->add($mentor);
        $auth->add($coach);
        $auth->add($moderator);

        $auth->addChild($hr, $emp);
        $auth->addChild($hr, $mentor);
        $auth->addChild($hr, $coach);
        $auth->addChild($admin, $hr);
        $auth->addChild($admin, $moderator);

        $auth->assign($admin, User::find()->where(['user_uuid' => User::SEED_ADMIN_UUID])->one()->id);
        /*$rating_create = $auth->createPermission('rating/create');
        $rating_read = $auth->createPermission('rating/read');
        $rating_update = $auth->createPermission('rating/update');
        $rating_delete = $auth->createPermission('rating/delete');

        $user_create = $auth->createPermission('user/create');
        $user_read = $auth->createPermission('user/read');
        $user_update = $auth->createPermission('user/update');
        $user_delete = $auth->createPermission('user/delete');

        $training_create = $auth->createPermission('training/create');
        $training_read = $auth->createPermission('training/read');
        $training_update = $auth->createPermission('training/update');
        $training_delete = $auth->createPermission('training/delete');

        $request_create = $auth->createPermission('request/create');
        $request_read = $auth->createPermission('request/read');
        $request_update = $auth->createPermission('request/update');
        $request_delete = $auth->createPermission('request/delete');

        $client_create = $auth->createPermission('client/create');
        $client_read = $auth->createPermission('client/read');
        $client_update = $auth->createPermission('client/update');
        $client_delete = $auth->createPermission('client/delete');

        $program_create = $auth->createPermission('program/create');
        $program_read = $auth->createPermission('program/read');
        $program_update = $auth->createPermission('program/update');
        $program_delete = $auth->createPermission('program/delete');

        $subject_create = $auth->createPermission('subject/create');
        $subject_read = $auth->createPermission('subject/read');
        $subject_update = $auth->createPermission('subject/update');
        $subject_delete = $auth->createPermission('subject/delete');

        $tariff_create = $auth->createPermission('tariff/create');
        $tariff_read = $auth->createPermission('tariff/read');
        $tariff_update = $auth->createPermission('tariff/update');
        $tariff_delete = $auth->createPermission('tariff/delete');

        $notification_create = $auth->createPermission('notification/create');
        $notification_read = $auth->createPermission('notification/read');
        $notification_update = $auth->createPermission('notification/update');
        $notification_delete = $auth->createPermission('notification/delete');*/

    }

    public function actionUuid($count): string
    {
        if ($count > 100) {
            return 'Вы указали слишком большое количество';
        }

        while($count) {
            echo Uuid::uuid6() . PHP_EOL;
            --$count;
        }

        return '';
    }

    public function actionSubjects(): void
    {
        $subjects = [
            [
                'subject_uuid' => '1eb70482-a0fd-6a32-88a7-0242563b8a75',
                'title' => 'Бизнес-мышление',
                'description' => 'Бизнес-мышление',
                'img_name' => 'business_mind',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-6438-9264-0242563b8a75',
                'title' => 'Готовность к изменениям',
                'description' => 'Готовность к изменениям',
                'img_name' => 'changing',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-669a-9dfa-0242563b8a75',
                'title' => 'Клиентоориентированность',
                'description' => 'Клиентоориентированность',
                'img_name' => 'clients',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-688e-86b1-0242563b8a75',
                'title' => 'Коммуникация',
                'description' => 'Коммуникация',
                'img_name' => 'communication',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-6a64-b15c-0242563b8a75',
                'title' => 'Корпоративная культура',
                'description' => 'Корпоративная культура',
                'img_name' => 'corporate_culture',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-6c26-bb8f-0242563b8a75',
                'title' => 'Лидерство',
                'description' => 'Лидерство',
                'img_name' => 'leadership',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-6dde-8e11-0242563b8a75',
                'title' => 'Мотивация команды',
                'description' => 'Мотивация команды',
                'img_name' => 'team_motivation',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a107-6fa0-9a6d-0242563b8a75',
                'title' => 'Организация работы',
                'description' => 'Организация работы',
                'img_name' => 'process',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb70482-a108-6162-a34c-0242563b8a75',
                'title' => 'Ориентация на результат',
                'description' => 'Ориентация на результат',
                'img_name' => 'result',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed1-d8b7-68be-a7d0-0242ac1e0004',
                'title' => 'Ответственность',
                'description' => 'Ответственность',
                'img_name' => 'responsibility',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed1-d8ca-6c98-9b84-0242ac1e0004',
                'title' => 'Планирование',
                'description' => 'Планирование',
                'img_name' => 'planning',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed1-d8ca-6f5e-8747-0242ac1e0004',
                'title' => 'Принятие решений',
                'description' => 'Принятие решений',
                'img_name' => 'decision-making',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed1-d8cb-6062-a277-0242ac1e0004',
                'title' => 'Проактивность',
                'description' => 'Проактивность',
                'img_name' => 'proactivity',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed1-d8cb-6134-948d-0242ac1e0004',
                'title' => 'Профессиональные знания',
                'description' => 'Профессиональные знания',
                'img_name' => 'knowledge',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed1-d8cb-61fc-a16e-0242ac1e0004',
                'title' => 'Работа в команде',
                'description' => 'Работа в команде',
                'img_name' => 'team',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed6-563d-6e24-8c35-0242ac1e0004',
                'title' => 'Развитие команды',
                'description' => 'Развитие команды',
                'img_name' => 'team_development',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed6-5648-6d6a-8a04-0242ac1e0004',
                'title' => 'Саморазвитие',
                'description' => 'Саморазвитие',
                'img_name' => 'upgrade',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed6-5648-6fb8-bc93-0242ac1e0004',
                'title' => 'Сотрудничество',
                'description' => 'Сотрудничество',
                'img_name' => 'collaboration',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed6-5649-60b2-8432-0242ac1e0004',
                'title' => 'Стратегическое мышление',
                'description' => 'Стратегическое мышление',
                'img_name' => 'strategic-mind',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed6-5649-617a-bd47-0242ac1e0004',
                'title' => 'Стрессоустойчивость',
                'description' => 'Стрессоустойчивость',
                'img_name' => 'strong',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed6-5649-6238-b296-0242ac1e0004',
                'title' => 'Управление командой',
                'description' => 'Управление командой',
                'img_name' => 'team_management',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb90ed8-e588-692e-9d90-0242ac1e0004',
                'title' => 'Этика и моральные ценности',
                'description' => 'Этика и моральные ценности',
                'img_name' => 'ethics',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ]
        ];

        UserSubject::deleteAll();
        Subject::deleteAll();

        Yii::$app->db->createCommand()->batchInsert(Subject::tableName(), [
            'subject_uuid',
            'title',
            'description',
            'img_name',
            'created_at',
            'created_by'
        ], $subjects)
            ->execute();
    }

    public function actionCompetencies(): void
    {
        $competence = [
            [
                'subject_uuid' => '1eb91131-7f67-600e-8fb9-0242ac1c0004',
                'title' => 'Личное развитие',
                'description' => 'Личное развитие',
                'img_name' => 'personal_development',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8038-6f82-9eec-0242ac1c0004',
                'title' => 'Эффективность',
                'description' => 'Эффективность',
                'img_name' => 'efficiency',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-6126-a9dc-0242ac1c0004',
                'title' => 'Постановка целей',
                'description' => 'Постановка целей',
                'img_name' => 'goals',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-6202-b1b5-0242ac1c0004',
                'title' => 'Осознанность',
                'description' => 'Осознанность',
                'img_name' => 'mindfulness',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-62c0-b678-0242ac1c0004',
                'title' => 'Здоровье и благополучие',
                'description' => 'Здоровье и благополучие',
                'img_name' => 'health',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-637e-bd87-0242ac1c0004',
                'title' => 'Лидерство',
                'description' => 'Лидерство',
                'img_name' => 'leadership',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-64c8-9f11-0242ac1c0004',
                'title' => 'Управление командой',
                'description' => 'Управление командой',
                'img_name' => 'team_management',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-65a4-9564-0242ac1c0004',
                'title' => 'Развивающий стиль руководства',
                'description' => 'Развивающий стиль руководства',
                'img_name' => 'up_management',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-666c-a244-0242ac1c0004',
                'title' => 'Коммуникация',
                'description' => 'Коммуникация',
                'img_name' => 'communication',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-6716-bb34-0242ac1c0004',
                'title' => 'Работа с конфликтами',
                'description' => 'Работа с конфликтами',
                'img_name' => 'working_with_conflicts',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ],
            [
                'subject_uuid' => '1eb91131-8039-67d4-b5a4-0242ac1c0004',
                'title' => 'Управление эмоциями',
                'description' => 'Управление эмоциями',
                'img_name' => 'emotions',
                'created_at' => time(),
                'created_by' => User::SEED_ADMIN_UUID
            ]
        ];

        UserCompetence::deleteAll();
        Competence::deleteAll();

        Yii::$app->db->createCommand()->batchInsert(Competence::tableName(), [
            'competence_uuid',
            'title',
            'description',
            'img_name',
            'created_at',
            'created_by'
        ], $competence)
            ->execute();
    }

}
