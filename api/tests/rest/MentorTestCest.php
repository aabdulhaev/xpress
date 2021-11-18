<?php

namespace rest;


use api\tests\RestTester;
use common\models\EmployeeMentor;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserProgramFixture;
use yii\helpers\Json;

/**
 * Class MentorTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class MentorTestCest
{

    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class,
            'mentor_assignments' => EmployeeMentorFixture::class,
            'user_programs' => UserProgramFixture::class
        ];
    }

    public function testCheckCRUDUnauthenticated(RestTester $I)
    {
        $I->sendGET('/mentor/connected');
        $I->seeResponseCodeIs(401);
    }

    public function testCheckCRUDUnauthorised(RestTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::MENT_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendGET('/mentor/connected');
        $I->seeResponseCodeIs(403);
    }


    public function testConnectedEmpToMentor(RestTester $I): void
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

        $I->sendGET('/mentor/connected');
        $I->seeResponseCodeIs(200);

        $content = Json::decode($I->grabResponse());

        expect('Ментор должен быть 1', $content)->count(1);
    }

    public function testUnconnectedEmpToMentor(RestTester $I): void
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

        $I->sendGET('/mentor/unconnected');
        $I->seeResponseCodeIs(200);

        $I->grabResponse();

        $content = Json::decode($I->grabResponse());
        expect('Ментор должен быть 1', $content)->count(1);
    }

    public function testCreateRequestEmpToMentor(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::EMP_AUTH_3['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPOST('/mentor/create-request/' . UserFixture::MENT_AUTH_2['id']);
        $I->seeResponseCodeIs(201);

        $I->seeResponseContainsJson(
            [
                'employee_uuid' => UserFixture::EMP_AUTH_3['id'],
                'mentor_uuid' => UserFixture::MENT_AUTH_2['id'],
                'status' => EmployeeMentor::STATUS_UNCONNECTED
            ]
        );
    }

    public function testCreateRequestEmpToMentorForbidden(RestTester $I): void
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

        $I->sendPOST('/mentor/create-request/' . UserFixture::MENT_AUTH_3['id']);
        $I->seeResponseCodeIs(403);
    }

    public function testEmployeeDeclineMentorForbidden(RestTester $I): void
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

        $I->sendPATCH('/mentor/decline-connect/' . UserFixture::MENT_AUTH_1['id']);
        $I->seeResponseCodeIs(403);

    }

    public function testEmployeeDeclineCoach(RestTester $I): void
    {

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_12['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/mentor/decline-connect/' . UserFixture::MENT_AUTH_12['id']);
        $I->seeResponseCodeIs(200);

        $I->sendGET('/mentor/connected');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Ментор должен быть 1', $content)->count(1);
    }

    public function testEmployeeApproveMentor(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_12['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/mentor/approve-connect/' . UserFixture::MENT_AUTH_12['id']);
        $I->seeResponseCodeIs(200);

        $I->sendGET('/mentor/connected');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Менторов должно быть 2', $content)->count(2);
    }


    public function testEmpToMentorContactForbidden(RestTester $I): void
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

        $I->sendPOST('/mentor/contact/' . UserFixture::MENT_AUTH_1['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(403);
    }

    public function testEmpToMentorContactInvalid(RestTester $I): void
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

        $I->sendPOST('/mentor/contact/' . UserFixture::MENT_AUTH_2['id'], ['body' => 'какой то текст сообщения']);
        $I->seeResponseCodeIs(422);
    }

    public function testEmpToMentorContactValid(RestTester $I): void
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

        $I->sendPOST(
            '/mentor/contact/' . UserFixture::MENT_AUTH_2['id'],
            ['body' => 'какой то текст сообщения длинее 25 символов']
        );
        $I->seeResponseCodeIs(201);
    }

    public function testHrToMentorChangeStatusActive(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::HR_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/mentor/change-status/' . UserFixture::MENT_AUTH_1['id'], ['status' => User::STATUS_ACTIVE]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson(
            [
                'status' => User::STATUS_ACTIVE
            ]
        );
    }

    public function testHrToMentorChangeStatusActiveBatch(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::HR_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $mentors = [
            UserFixture::MENT_AUTH_1['id'],
            UserFixture::MENT_AUTH_2['id']
        ];

        foreach ($mentors as $mentor) {
            $I->sendPATCH('/mentor/change-status/' . $mentor, ['status' => User::STATUS_ACTIVE]);
            $I->seeResponseCodeIs(200);
            $I->seeResponseContainsJson(['status' => User::STATUS_ACTIVE]);
        }
    }

    public function testHrToMentorChangeStatusInactive(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::HR_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/mentor/change-status/' . UserFixture::MENT_AUTH_1['id'], ['status' => User::STATUS_INACTIVE]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson(
            [
                'status' => User::STATUS_INACTIVE
            ]
        );
    }

    public function testHrToMentorChangeStatusSuspended(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/auth/login',
            [
                'login' => UserFixture::HR_AUTH_1['l'],
                'password' => UserFixture::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);

        $I->sendPATCH('/mentor/change-status/' . UserFixture::MENT_AUTH_1['id'], ['status' => User::STATUS_SUSPENDED]);
        $I->seeResponseCodeIs(200);

        $I->seeResponseContainsJson(
            [
                'status' => User::STATUS_SUSPENDED
            ]
        );
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
            '/mentor/connect',
            [
                'mentors_uuid' => [
                    UserFixture::MENT_AUTH_10['id'],
                    UserFixture::MENT_AUTH_11['id'],
                    UserFixture::MENT_AUTH_12['id'],
                    UserFixture::MENT_AUTH_13['id'],
                    UserFixture::MENT_AUTH_14['id'],
                    UserFixture::MENT_AUTH_15['id'],
                    UserFixture::MENT_AUTH_16['id'],
                    UserFixture::MENT_AUTH_17['id'],
                    UserFixture::MENT_AUTH_18['id'],
                    UserFixture::MENT_AUTH_19['id'],
                    UserFixture::MENT_AUTH_20['id'],
                    UserFixture::MENT_AUTH_21['id'],
                    UserFixture::MENT_AUTH_22['id'],
                    UserFixture::MENT_AUTH_23['id'],
                    UserFixture::MENT_AUTH_24['id'],
                    UserFixture::MENT_AUTH_25['id'],
                    UserFixture::MENT_AUTH_26['id'],
                    UserFixture::MENT_AUTH_27['id'],
                    UserFixture::MENT_AUTH_28['id'],
                    UserFixture::MENT_AUTH_29['id'],
                    UserFixture::MENT_AUTH_30['id'],
                    UserFixture::MENT_AUTH_31['id'],
                    UserFixture::MENT_AUTH_32['id'],
                    UserFixture::MENT_AUTH_33['id'],
                    UserFixture::MENT_AUTH_34['id'],
                    UserFixture::MENT_AUTH_35['id'],
                    UserFixture::MENT_AUTH_36['id'],
                    UserFixture::MENT_AUTH_37['id'],
                    UserFixture::MENT_AUTH_38['id'],
                    UserFixture::MENT_AUTH_39['id'],

                ],
                'employees_uuid' => [
                    '1eb24282-0676-61b0-ead6-20c00c46f673',
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
    }


}
