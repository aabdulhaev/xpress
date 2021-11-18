<?php

declare(strict_types=1);

namespace rest;

use api\tests\RestTester;
use common\tests\fixtures\SubjectFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserSubjectFixture;

/**
 * Class ProfileTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class ProfileTestCest
{

    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
            'subjects' => SubjectFixture::class,
           // 'userSubjects' => UserSubjectFixture::class
        ];
    }

    public function testUnauthorizedUploadAvatar(RestTester $I)
    {
        $I->sendPOST(
            '/profile/change-avatar',
            [],
            ['avatar' => codecept_data_dir('file.png')]
        );
        $I->seeResponseCodeIs(401);
    }

    /**
     * Тест на 400 так как Yii не может сохранять файлы загруженные с тестов.
     * @param RestTester $I
     * @throws \Exception
     */
    public function testAuthorizedUploadAvatar(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::ADMIN_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->haveHttpHeader('content-type', 'multipart/form-data');
        $I->sendPOST(
            '/profile/change-avatar',
            [],
            ['avatar' => codecept_data_dir('avatar.jpg')]
        );
        $I->seeResponseCodeIs(400);
    }

    public function testUnauthorizedRemoveAvatar(RestTester $I)
    {
        $I->sendDELETE('/profile/remove-avatar');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @param RestTester $I
     * @throws \Exception
     */
    public function testAuthorizedRemoveAvatar(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::ADMIN_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->haveHttpHeader('content-type', 'multipart/form-data');
        $I->sendDELETE('/profile/remove-avatar');
        $I->seeResponseCodeIs(204);
    }

    public function testUnauthorizedChangePassword(RestTester $I)
    {
        $I->sendPATCH('/profile/password', ['password' => 'newpass']);
        $I->seeResponseCodeIs(401);
    }

    public function testAuthorizedInvalidChangePassword(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::ADMIN_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/profile/password', ['password' => ' newpass ']);
        $I->seeResponseCodeIs(422);
    }

    public function testAuthorizedValidChangePassword(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::ADMIN_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/profile/password', ['password' => 'newpass__']);
        $I->seeResponseCodeIs(200);
    }


    public function testAuthorizedValidAssignSubject(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::EMP_AUTH_2['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST(
            '/profile/assign-subject',
            ['subjects' => [SubjectFixture::SUB_4_UUID, SubjectFixture::SUB_3_UUID]]
        );
        $I->seeResponseCodeIs(201);
    }

    public function testAuthorizedStatistic(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::EMP_AUTH_2['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/profile/stats');
        $I->seeResponseCodeIs(200);
    }

    public function testAuthorizedView(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::EMP_AUTH_2['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/profile/view' );
        $I->seeResponseCodeIs(200);
    }
}
