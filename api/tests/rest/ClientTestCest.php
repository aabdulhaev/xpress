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
 * Class ClientTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class ClientTestCest
{

    public function _fixtures(): array
    {
        return [
            'clients' => ClientFixture::class,
            'users' => UserFixture::class
        ];
    }

    public function testClientIndex(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/clients');
        $I->seeResponseCodeIs(200);
    }

    public function testClientCreate(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/clients',[
            'name' => 'Test Company'
        ]);
        $I->seeResponseCodeIs(201);
    }

    public function testClientView(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGet('/clients/' . ClientFixture::CLIENT_1_UUID);
        $I->seeResponseCodeIs(200);
    }

    public function testClientUpdate(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/clients/' . ClientFixture::CLIENT_1_UUID,['name' => 'Новое имя']);
        $I->seeResponseCodeIs(200);
    }
}
