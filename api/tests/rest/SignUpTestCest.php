<?php

namespace rest;


use api\tests\RestTester;
use common\access\Rbac;
use common\models\Client;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\UserFixture;
use Exception;

/**
 * Class SignUpTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class SignUpTestCest
{

    public function _fixtures() : array
    {
        return [
            'users'   => UserFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class
        ];
    }

    public function testAddInvalidEmployeeByHr(RestTester $I): void
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
        $I->sendPOST('/signup/employee', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_EMP,
            'client_uuid' => ClientFixture::CLIENT_2_UUID
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testAddValidEmployeeByHr(RestTester $I): void
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
        $I->sendPOST('/signup/employee', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'department' => 'Тестирование',
            'position' => 'QA инжинер'
        ]);
        $I->seeResponseCodeIs(201);
    }

    public function testAddInvalidMentorByHr(RestTester $I): void
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
        $I->sendPOST('/signup/mentor', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_ADMIN,
            'client_uuid' => ClientFixture::CLIENT_2_UUID
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testAddValidMentorByHr(RestTester $I): void
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
        $I->sendPOST('/signup/mentor', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'department' => 'Тестирование',
            'position' => 'QA инжинер'
        ]);
        $I->seeResponseCodeIs(201);

    }

    public function testInvalidConfirmByEmployee(RestTester $I): void
    {
        $token = 'djnjiwq';
        $I->sendGET('/signup/confirm', [
            'token' => $token,
        ]);
        $I->seeResponseCodeIs(404);

    }

    public function testValidConfirmByEmployee(RestTester $I): void
    {
        $token = '1eb150ad-eb40-6c90-d8a5-ce057c5995d2';
        $I->sendGET('/signup/confirm', [
            'token' => $token,
        ]);
        $I->seeResponseCodeIs(201);
    }

}
