<?php

namespace unit\models;

use Codeception\Test\Unit;
use common\dispatchers\EventDispatcher;
use common\dispatchers\SimpleEventDispatcher;
use common\forms\AssignProgramForm;
use common\models\EmployeeMentor;
use common\models\User;
use common\repositories\EmployeeMentorRepository;
use common\repositories\UserRepository;
use common\services\TransactionManager;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\ProgramFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserProgramFixture;
use common\tests\UnitTester;
use common\useCases\UserManageCase;

class EmployeeProgramTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;
    protected $useCase;

    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            'users'       => UserFixture::class,
            'employee_mentor' => EmployeeMentorFixture::class,
            'user_program' => UserProgramFixture::class
        ];
    }

    public function testEmployeeMentoringAndCoaching()
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();

        $fields = $user->toArray();

        expect('Сотрудник добавлен в программу менторства', $fields['mentor_program'])->true();
        expect('Сотрудник добавлен в программу коучинга', $fields['coach_program'])->true();
    }

    public function testEmployeeMentoring()
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_2['id']]);
        expect('Пользователь найден', $user)->notEmpty();

        $fields = $user->toArray();

        expect('Сотрудник не добавлен в программу менторства', $fields['mentor_program'])->false();
        expect('Сотрудник добавлен в программу коучинга', $fields['coach_program'])->true();
    }

    public function testEmployeeNotProgram()
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_3['id']]);
        expect('Пользователь найден', $user)->notEmpty();

        $fields = $user->toArray();

        expect('Сотрудник не добавлен в программу менторства', $fields['mentor_program'])->false();
        expect('Сотрудник не добавлен в программу коучинга', $fields['coach_program'])->false();
    }

}
