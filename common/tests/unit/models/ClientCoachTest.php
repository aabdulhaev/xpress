<?php

namespace unit\models;

use Codeception\Test\Unit;
use common\models\Client;
use common\models\EmployeeMentor;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\ClientCoachFixture;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\UserFixture;
use common\tests\UnitTester;

class ClientCoachTest extends Unit
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
            'clients' => ClientFixture::class,
            'client_assignments' => ClientCoachFixture::class,
            'user_assignments' => EmployeeMentorFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class,
        ];
    }

    public function testEmployeeCoachRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_2['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список всех коучей у сотрудника', $user->coaches)->count(2);
    }

    public function testClientCoachRelation(): void
    {
        $client = Client::findOne(['client_uuid' => ClientFixture::CLIENT_3_UUID]);
        expect('Компания ', $client)->notEmpty();
        expect('Список коучей доступных сотрудникам компании', $client->coaches)->count(3);
    }

    public function testHrCoachRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::HR_AUTH_3['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список сотрудников у айчара', $user->clientCoaches)->count(3);
    }

    public function testEmployeeCoachRelationActive(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список cвязаных коучей', $user->activeCoaches)->count(1);
    }

    public function testEmployeeCoachRelationDecline(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список отклоненных коучей', $user->declineCoaches)->count(1);
    }

    public function testEmployeeCoachRelationDraft(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список отклоненных коучей', $user->draftCoaches)->count(1);
    }

    public function testEmployeeCoachRelationUnconnected(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список доступных коучей', $user->unconnectedCoaches)->count(2);
    }

    public function testCoachEmployeeRelationActive(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список одобренных сотрудников у коуча', $user->activeEmployees)
            ->count(2);
    }

    public function testCoachEmployeeRelationDecline(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список отклоненных сотрудников у коуча', $user->declineEmployees)
            ->count(1);
    }

    public function testCoachEmployeeRelationDraft(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_3['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список неодобренных сотрудников у коуча', $user->draftEmployees)
            ->count(1);
    }

    public function testCoachEmployeeRelation(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список всех сотрудников у коуча', $user->employees)->count(3);
    }

}
