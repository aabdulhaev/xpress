<?php

namespace rest;


use api\tests\RestTester;
use common\access\Rbac;
use common\models\Client;
use common\models\Request;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\RequestFixture;
use common\tests\fixtures\UserFixture;
use Exception;

/**
 * Class RequestTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class RequestTestCest
{

    public function _fixtures(): array
    {
        return [
            'requests' => RequestFixture::class
        ];
    }

    public function testValidCreateCoach(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/request/coach', [
            'name' => 'Новая заявка',
            'email' => 'newcoach@test.loc',
            'description' => 'Хочу стать тренером',
            'phone' => '896775800022'
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testValidCreateClient(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/request/client', [
            'name' => 'Новая заявка',
            'email' => 'newclient@test.loc',
            'description' => 'Хочу стать клиентов',
            'phone' => '896775800033'
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testInvalidCreateCoach(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/request/coach', [
            'email' => 'newcoach@test.loc',
            'description' => 'Хочу стать тренером',
            'phone' => '896775800022',
            'status' => Request::STATUS_APPROVED
        ]);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
    }

    public function testInvalidCreateClient(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/request/client', [
            'name' => 'Новая заявка',
            'description' => 'Хочу стать клиентов',
            'phone' => '896775800033'
        ]);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
    }



    public function testValidApproved(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/request/approve/' . RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testValidDecline(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/request/decline/' . RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }


    public function testUnauthenticatedChangeStatus(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/request/decline/' . RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }

    public function testUnauthorisedChangeStatus(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/request/decline/' . RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }


    public function testCheckUnauthenticatedIndex(RestTester $I)
    {
        $I->sendGET('/request/index');
        $I->seeResponseCodeIs(401);
    }

    public function testCheckUnauthenticatedView(RestTester $I)
    {
        $I->sendGET('/request/view/'. RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(401);
    }


    public function testCheckUnauthorisedIndex(RestTester $I)
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


        $I->sendGET('/request/index');
        $I->seeResponseCodeIs(403);
    }

    public function testCheckUnauthorisedView(RestTester $I)
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


        $I->sendGET('/request/view/' . RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(403);
    }

    public function testCheckIndex(RestTester $I)
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


        $I->sendGET('/request/index');
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['request_uuid' => 'string', 'email' => 'string'], '$[*]');
    }

    public function testCheckView(RestTester $I)
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

        $I->sendGET('/request/view/' . RequestFixture::COACH_UUID);
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['request_uuid' => 'string', 'email' => 'string']);
    }

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

        $I->sendGET('/request/index', ['filter' => ['email' => 'coach@test.loc']]);
        $I->seeResponseCodeIs(200);

        $content = json_decode($I->grabResponse());

        expect('Пользователь с указанным email должен быть только один', $content)->count(1);
    }

    public function testSort(RestTester $I)
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

        $I->sendGET('/request/index', ['sort' => '-name']);
        $I->seeResponseCodeIs(200);
    }
}
