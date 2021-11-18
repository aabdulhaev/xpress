<?php

namespace unit\models;

use Codeception\Test\Unit;
use common\models\EmployeeMentor;
use common\models\User;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\UserFixture;
use common\tests\UnitTester;

class EmployeeMentorTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;


    /**
     * @return array
     */
    public function _fixtures(): array
    {
        return [
            'users'       => UserFixture::class,
            'employee_mentor' => EmployeeMentorFixture::class
        ];
    }

    public function testEmployeeMentorsRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_2['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список менторов у сотрудника', $user->mentors)->count(2);
    }

    public function testEmployeeCoachesRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_2['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список коучей у сотрудника', $user->coaches)->count(2);
    }

    public function testMentorEmployeeRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::MENT_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у ментора', $user->employees)->count(3);
    }

    public function testMentorEmployeeActiveRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::MENT_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у ментора', $user->activeEmployees)
            ->count(1);
    }

    public function testMentorEmployeeDeclineRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::MENT_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у ментора', $user->declineEmployees)
            ->count(1);
    }

    public function testMentorEmployeeDraftRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::MENT_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у ментора', $user->draftEmployees)
            ->count(1);
    }

    public function testHrEmployeeRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::HR_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у айчара', $user->clientEmployees)->count(3);
    }

    public function testHrMentorsRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::HR_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у айчара', $user->clientMentors)->count(2);
    }

    public function testEmployeeMentorsUnconnected(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $result = $user->getUnconnectedMentors()->asArray()->all();
        expect('Список не подключенных менторов той же компании что и сотрудник', $result)->count(1);
    }
}
