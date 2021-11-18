<?php

namespace rest;


use api\tests\RestTester;
use common\access\Rbac;
use common\models\Client;
use common\models\EmployeeMentor;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\ClientCoachFixture;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\ProgramFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserProgramFixture;
use Exception;
use yii\helpers\Json;

/**
 * Class CoachTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class CoachTestCest
{

    public function _fixtures() : array
    {
        return [
            'users'   => UserFixture::class,
            'clients' => ClientFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class,
            'mentor_assignments' => EmployeeMentorFixture::class,
            'user_programs' => UserProgramFixture::class,
            'coaches' => ClientCoachFixture::class,
        ];
    }

    public function testCheckCRUDUnauthenticated(RestTester $I)
    {
        $I->sendGET('/coach/connected');
        $I->seeResponseCodeIs(401);
    }

    public function testCheckCRUDUnauthorised(RestTester $I)
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

        $I->sendGET('/coach/connected');
        $I->seeResponseCodeIs(403);
    }


    public function testConnectedEmpToCoach(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/connected');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('Коуч должен быть 1', $content)->count(1);
    }

    public function testUnconnectedEmpToCoach(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/unconnected');
        $I->seeResponseCodeIs(200);

        $I->grabResponse();

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должен быть 2', $content)->count(2);
    }

    public function testEmployeeDeclineCoachForbidden(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_2['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/coach/decline-connect/' . UserFixture::COACH_AUTH_3['id']);
        $I->seeResponseCodeIs(403);

    }

    public function testEmployeeDeclineCoach(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/coach/decline-connect/' . UserFixture::COACH_AUTH_1['id']);
        $I->seeResponseCodeIs(200);

        $I->sendGET('/coach/connected');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей не должно быть', $content)->count(0);
    }

    public function testEmployeeApproveCoach(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/coach/approve-connect/' . UserFixture::COACH_AUTH_3['id']);
        $I->seeResponseCodeIs(200);

        $I->sendGET('/coach/connected');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 2', $content)->count(2);
    }

    public function testCreateRequestEmpToCoach(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::EMP_AUTH_11['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/create-request/' . UserFixture::COACH_AUTH_2['id']);
        $I->seeResponseCodeIs(201);

        $I->seeResponseContainsJson(
            [
                'employee_uuid' => UserFixture::EMP_AUTH_11['id'],
                'mentor_uuid' => UserFixture::COACH_AUTH_2['id'],
                'status' => EmployeeMentor::STATUS_UNCONNECTED
            ]
        );
    }

    public function testCreateRequestEmpToCoachForbidden(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::EMP_AUTH_2['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/create-request/' . UserFixture::COACH_AUTH_1['id']);
        $I->seeResponseCodeIs(403);
    }

    public function testEmpToCoachContactForbidden(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_2['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/contact/'. UserFixture::COACH_AUTH_3['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(403);

    }

    public function testEmpToCoachContactInvalid(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/contact/'. UserFixture::COACH_AUTH_1['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(422);

    }

    public function testEmpToCoachContactValid(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/contact/'. UserFixture::COACH_AUTH_1['id'], ['body' => 'какой то текст сообщения длинее 25 символов']);
        $I->seeResponseCodeIs(201);

    }

    public function testHrCoachList(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/index/');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 3', $content)->count(3);
    }

    public function testHrCoachListPagination(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/index?per-page=2');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 2', $content)->count(2);


        $I->sendGET('/coach/index?per-page=2&page=2');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 1', $content)->count(1);
    }

    public function testHrCoachListForbidden(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/index/');
        $I->seeResponseCodeIs(403);
    }

    public function testHrCoachNewestList(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/newest/');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 3', $content)->count(3);
    }

    public function testAddNewCoachesToClient(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/add', ['coaches_uuid' => [
            UserFixture::COACH_AUTH_4['id'],
            UserFixture::COACH_AUTH_5['id'],
            UserFixture::COACH_AUTH_6['id']
        ]]);
        $I->seeResponseCodeIs(201);

        $I->sendGET('/coach/index/');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 6', $content)->count(6);
    }

    public function testAddNewCoachesToClientOneError(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/add', ['coaches_uuid' => [
            UserFixture::COACH_AUTH_3['id'],
            UserFixture::COACH_AUTH_5['id'],
            UserFixture::COACH_AUTH_6['id']
        ]]);
        $I->seeResponseCodeIs(201);

        $I->sendGET('/coach/index/');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 5', $content)->count(5);
    }

    public function testRemoveCoachesFromClient(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/coach/remove', ['coaches_uuid' => [
            UserFixture::COACH_AUTH_3['id'],
            UserFixture::COACH_AUTH_2['id'],
            UserFixture::COACH_AUTH_6['id']
        ]]);

        $I->seeResponseCodeIs(204);

        $I->sendGET('/coach/index/');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Коучей должно быть 1', $content)->count(1);
    }

    public function testHrCreateConnectBatch(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::HR_AUTH_3['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST(
            '/coach/connect',
            [
                'coaches_uuid' => [
                    UserFixture::COACH_AUTH_1['id'],
                    UserFixture::COACH_AUTH_2['id'],
                    UserFixture::COACH_AUTH_3['id']

                ],
                'employees_uuid' => [
                    UserFixture::EMP_AUTH_10['id'],
                    UserFixture::EMP_AUTH_11['id'],
                    UserFixture::EMP_AUTH_12['id'],
                    UserFixture::EMP_AUTH_13['id'],
                    UserFixture::EMP_AUTH_14['id'],
                    UserFixture::EMP_AUTH_15['id'],
                    UserFixture::EMP_AUTH_16['id'],
                    UserFixture::EMP_AUTH_17['id'],
                    UserFixture::EMP_AUTH_18['id'],
                    UserFixture::EMP_AUTH_19['id'],
                    UserFixture::EMP_AUTH_20['id'],
                    UserFixture::EMP_AUTH_21['id'],
                    UserFixture::EMP_AUTH_22['id'],
                    UserFixture::EMP_AUTH_23['id'],
                    UserFixture::EMP_AUTH_24['id'],
                    UserFixture::EMP_AUTH_25['id'],
                    UserFixture::EMP_AUTH_26['id'],
                    UserFixture::EMP_AUTH_27['id'],
                    UserFixture::EMP_AUTH_28['id'],
                    UserFixture::EMP_AUTH_29['id'],
                    UserFixture::EMP_AUTH_30['id'],
                    UserFixture::EMP_AUTH_31['id'],
                    UserFixture::EMP_AUTH_32['id'],
                    UserFixture::EMP_AUTH_33['id'],
                    UserFixture::EMP_AUTH_34['id'],
                    UserFixture::EMP_AUTH_35['id'],
                    UserFixture::EMP_AUTH_36['id'],
                    UserFixture::EMP_AUTH_37['id'],
                    UserFixture::EMP_AUTH_38['id'],
                    UserFixture::EMP_AUTH_39['id'],
                ]
            ]
        );
        $I->seeResponseCodeIs(201);

        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_15['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/coach/connected');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('Коуч должен быть 3', $content)->count(3);



    }


}
