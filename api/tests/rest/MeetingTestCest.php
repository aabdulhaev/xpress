<?php

namespace rest;


use api\tests\RestTester;
use common\access\Rbac;
use common\models\Client;
use common\models\TrainingSession;
use common\models\User;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\ClientCoachFixture;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\SubjectFixture;
use common\tests\fixtures\TrainingSessionFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserTrainingFixture;
use Exception;
use yii\helpers\Json;

/**
 * Class MeetingTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class MeetingTestCest
{

    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
            'clients' => ClientFixture::class,
            'client_assignments' => ClientCoachFixture::class,
            'user_assignments' => EmployeeMentorFixture::class,
            'auth_assignments' => AuthAssignmentFixture::class,
            'training' => TrainingSessionFixture::class,
            'user_training' => UserTrainingFixture::class,
            'subject' => SubjectFixture::class
        ];
    }

    public function testMentorStartMeeting(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST("/meeting/start?XDEBUG_SESSION_START=18016",[
            'training_uuid' => '1eb2fe05-341c-60d1-21f3-ccb0c31152e5',
            'start_at' => '2021-03-02 14:40:00'
        ]);
        $I->seeResponseCodeIs(200);

        $content = Json::decode($I->grabResponse());
        expect('Ответ должен быть строкой', $content)->string();

        var_dump($content);
    }

    public function testEmpJoinMeeting(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET("/meeting/join?training_uuid=1eb2fe05-341c-60d1-21f3-ccb0c31152e5");
        $I->seeResponseCodeIs(200);

        $content = Json::decode($I->grabResponse());
        expect('Ответ должен быть строкой', $content)->string();

        var_dump($content);
    }


}
