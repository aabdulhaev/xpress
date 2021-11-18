<?php

namespace rest;


use api\tests\RestTester;
use common\access\Rbac;
use common\models\Client;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\ProgramFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserProgramFixture;
use Exception;
use yii\helpers\Json;

/**
 * Class EmployeeMentorTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class EmployeeTestCest
{

    public function _fixtures() : array
    {
        return [
            'users'   => UserFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class,
            'assignments' => EmployeeMentorFixture::class,
            'user_programs' => UserProgramFixture::class
        ];
    }

    public function testCheckCRUDUnauthenticated(RestTester $I)
    {
        $I->sendGET('/employee/connected');
        $I->seeResponseCodeIs(401);
    }

    public function testCheckCRUDUnauthorised(RestTester $I)
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/employee/connected');
        $I->seeResponseCodeIs(403);
    }


    public function testConnectedEmpToMentor(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/employee/connected');
        $I->seeResponseCodeIs(200);

        $I->grabResponse();

        $content =  Json::decode($I->grabResponse());

        expect('Сотрудник должен быть 1', $content)->count(1);
    }

    public function testRequestingEmpToMentor(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/employee/requesting');
        $I->seeResponseCodeIs(200);

        $I->grabResponse();

        $content =  Json::decode($I->grabResponse());

        expect('Сотрудник должен быть 1', $content)->count(1);
    }

    public function testEmpToHr(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/employee/index');
        $I->seeResponseCodeIs(200);

        $I->grabResponse();

        $content =  Json::decode($I->grabResponse());

        expect('Сотрудник должен быть 1', $content)->count(3);
    }

    public function testMentorToEmpContactInvalid(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/employee/contact/'. UserFixture::EMP_AUTH_2['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(422);

    }

    public function testMentorToEmpContactValid(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/employee/contact/'. UserFixture::EMP_AUTH_2['id'], ['body' => 'какой то текст сообщения длинее 25 символов']);
        $I->seeResponseCodeIs(201);

    }


    public function testMentorToEmpInviteForbidden(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/employee/invite/'. UserFixture::EMP_AUTH_2['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(403);

    }

    public function testMentorToEmpInviteInvalid(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/employee/invite/'. UserFixture::EMP_AUTH_2['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(422);

    }

    public function testMentorToEmpInviteValid(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/employee/invite/'. UserFixture::EMP_AUTH_2['id'], ['body' => 'какой то текст сообщения длинее 25 символов']);
        $I->seeResponseCodeIs(201);

    }

    public function testMentorToEmpApproveConnect(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/approve-connect/'. UserFixture::EMP_AUTH_2['id']);
        $I->seeResponseCodeIs(200);

    }

    public function testMentorToEmpDeclineConnect(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::MENT_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/decline-connect/'. UserFixture::EMP_AUTH_2['id'],['comment' => 'коммент при отклонении связи']);
        $I->seeResponseCodeIs(200);

    }


    public function testHrToEmpChangeStatusActive(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/change-status/'. UserFixture::EMP_AUTH_2['id'],['status' => User::STATUS_ACTIVE]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'status' => User::STATUS_ACTIVE
        ]);
    }

    public function testHrToEmpChangeStatusInactive(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/change-status/'. UserFixture::EMP_AUTH_2['id'],['status' => User::STATUS_INACTIVE]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'status' => User::STATUS_INACTIVE
        ]);
    }

    public function testHrToEmpChangeStatusSuspended(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/change-status/'. UserFixture::EMP_AUTH_2['id'],['status' => User::STATUS_SUSPENDED]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'status' => User::STATUS_SUSPENDED
        ]);
    }


    public function testHrToEmpProgramAddMentorAndCoach(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/program/'. UserFixture::EMP_AUTH_3['id'],
              [
                  'programs' => [
                       ProgramFixture::MENTOR_UUID => 1,
                  ]
              ]);

        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'coach_program' => false,
            'mentor_program' => true,
        ]);

    }

    public function testHrToEmpProgramAddMentor(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/program/'. UserFixture::EMP_AUTH_2['id'],
                      [
                          'programs' => [
                              ProgramFixture::MENTOR_UUID => 1,
                              ProgramFixture::COACH_UUID => 1,
                          ]
                      ]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'coach_program' => true,
            'mentor_program' => true,
        ]);
    }

    public function testHrToEmpProgramRemoveCoach(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/program/'. UserFixture::EMP_AUTH_1['id'],
                      [
                          'programs' => [
                              ProgramFixture::COACH_UUID => 0,
                              ProgramFixture::MENTOR_UUID => 1,
                          ]
                      ]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'coach_program' => false,
            'mentor_program' => true,
        ]);
    }

    public function testHrToEmpProgramRemoveAll(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/employee/program/'. UserFixture::EMP_AUTH_3['id'],
                      [
                          'programs' => [
                              ProgramFixture::COACH_UUID => 0,
                              ProgramFixture::MENTOR_UUID => 0,
                          ]
                      ]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson([
            'coach_program' => false,
            'mentor_program' => false,
            'status' => User::STATUS_SUSPENDED
        ]);
    }


}
