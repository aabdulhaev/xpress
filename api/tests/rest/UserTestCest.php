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
class UserTestCest
{

    public function _fixtures() : array
    {
        return [
            'users'   => UserFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class
        ];
    }

    public function testCheckCRUDUnauthenticated(RestTester $I)
    {
        $I->sendGET('/users');
        $I->seeResponseCodeIs(401);

        $I->sendGET('/users/1eb14784-18ec-6a00-8c0e-d0f21dc4ef1a');
        $I->seeResponseCodeIs(401);

        $I->sendPOST('/users', ['first_name' => 'test']);
        $I->seeResponseCodeIs(401);

        $I->sendPATCH('/users/1eb14784-18ec-6a00-8c0e-d0f21dc4ef1a', ['first_name' => 'test_new_name']);
        $I->seeResponseCodeIs(401);

        $I->sendDELETE('/users/1eb14784-18ec-6a00-8c0e-d0f21dc4ef1a');
        $I->seeResponseCodeIs(401);
    }

    public function testCheckCRUDUnauthorised(RestTester $I)
    {
        // Удалять и добавлять может только админ
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/users');
        $I->seeResponseCodeIs(403);

        $I->sendGET('/users/' . UserFixture::HR_AUTH_1['id']);
        $I->seeResponseCodeIs(403);

        $I->sendPATCH('/users/' . UserFixture::HR_AUTH_1['id'], ['first_name' => 'test_new_name']);
        $I->seeResponseCodeIs(403);

        $I->sendDELETE('/users/'. UserFixture::HR_AUTH_1['id']);
        $I->seeResponseCodeIs(403);
    }


    public function testAddValidHrByAdmin(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/users', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_HR,
            'client_uuid' => ClientFixture::CLIENT_1_UUID
        ]);
        $I->seeResponseCodeIs(201);
    }

    public function testAddValidCoachByAdmin(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/users', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_COACH,
            'level' => User::LEVEL_PCC,
            'certification' => 'SERTIFICATED',
        ]);
        $I->seeResponseCodeIs(201);
    }

    public function testAddInvalidCoachByAdmin(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/users', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_COACH,
            'level' => 100
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testAddInvalidUserByAdmin(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/users', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_HR,
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testAddHrByHr(RestTester $I): void
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
        $I->sendPOST('/users', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.loc',
            'role' => Rbac::ROLE_HR,
            'client_uuid' => ClientFixture::CLIENT_2_UUID
        ]);
        $I->seeResponseCodeIs(403);
    }


    /**
     * @param RestTester $I
     * @throws Exception
     */
    public function testCheckCRUD(RestTester $I)
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);


        $I->sendGET('/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['user_uuid' => 'string', 'email' => 'string'], '$[*]');

        $I->sendGET('/users/' . UserFixture::HR_AUTH_1['id']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['user_uuid' => 'string', 'email' => 'string']);

       /* $I->sendPOST(
            '/users',
            [
                'password'   => 'password',
                'email'      => 'foo@bar.gz',
                'status'     => User::STATUS_ACTIVE,
                'first_name' => 'Пантелеймон'
            ]
        );
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['email' => 'foo@bar.gz']);

        $I->sendPATCH('/users/2', ['last_name' => 'Христорождественский']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['last_name' => 'Христорождественский']);

        $I->sendDELETE('/users/2');
        $I->seeResponseCodeIs(204);

        $I->sendGET('/users/2');
        $I->seeResponseContainsJson(['status' => User::STATUS_DELETED]);*/
    }

    /*public function testExpand(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => 'nicole.paucek@schultz.info',
            'password' => 'password_0'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);



        $I->sendGET('/users/1eb14784-18ef-6110-30fc-6cfd67b18d05', ['expand' => 'roles,lessons,groups,courses']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['roles' => 'array']);
        $I->seeResponseJsonMatchesJsonPath('$.lessons[*].progress');
        $I->seeResponseJsonMatchesJsonPath('$.groups[*].id');
        $I->seeResponseJsonMatchesJsonPath('$.courses[]');
    }*/

    public function testFilter(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);



        $I->sendGET('/users', ['filter' => ['email' => UserFixture::EMP_AUTH_2['l']]]);
        $I->seeResponseCodeIs(200);

        $content = json_decode($I->grabResponse());
        expect('Пользователь с указанным email должен быть только один', $content)->count(1);
    }

}
