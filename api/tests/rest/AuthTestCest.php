<?php

namespace rest;


use api\tests\RestTester;
use common\access\Rbac;
use common\models\Client;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\UserFixture;
use Exception;

/**
 * Class AuthTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class AuthTestCest
{

    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class
        ];
    }

    public function testCorsHeaders(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Origin', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);

        $I->seeHttpHeader('Access-Control-Allow-Origin', '*');

    }

    public function testValidLogin(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::ADMIN_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('access_token');
    }

    public function testInvalidLogin(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => 'nicole.paucek@schultz.info',
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testValidResetPasswordRequest(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/reset', [
            'email' => UserFixture::ADMIN_AUTH_1['l']
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testInvalidResetPasswordRequest(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/reset', [
            'email' => UserFixture::ADMIN_AUTH_2['l']
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testValidResetPasswordConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/confirm/1eb150ad_1803462516', [
            'password' => 'newpuuwd'
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testInvalidResetPasswordConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/confirm/1eb150ad_180346251', [
            'password' => 'newpuuwd'
        ]);
        $I->seeResponseCodeIs(400);
    }
}
