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
 * Class ClientTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class PlanningTestCest
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

    public function testEmployeeConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/confirmed');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
            echo 'Id: ' . $training['training_uuid'] . PHP_EOL;
            echo 'User: ' . UserFixture::EMP_AUTH_10['l'] . PHP_EOL;
            echo 'Start: ' . $training['start_at'] .  PHP_EOL;
            echo 'End: ' . $training['end_at']. PHP_EOL;
        }
    }

    public function testEmployeeConfirmedFilter(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/confirmed',['start_at_from' => '2020-11-02','start_at_to' => '2020-11-12']);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 0', $content)->count(0);



        $I->sendGET('/planning/confirmed',['start_at_from' => '2020-12-01','start_at_to' => '2021-02-12']);
        $I->seeResponseCodeIs(200);
        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);


        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
        }
    }

    public function testEmployeeForRating(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/for-rating');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_5);
        }
    }

    public function testEmployeeFree(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/free');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_1);
        }
    }

    public function testEmployeeWaitConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/wait-confirm');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_2);
        }
    }

    public function testEmployeeNeedConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/need-confirm');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);
        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_7);
        }
    }

    public function testEmployeeCancelConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/cancel/'.TrainingSessionFixture::TRAINING_UUID_7, ['comment' => 'Простите не успеваю.']);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_7);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_CANCEL);

    }

    public function testEmployeeMoveConfirmedValid(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/move/'.TrainingSessionFixture::TRAINING_UUID_3, [
            'start_at' => '2020-12-12 10:00',
            'comment' => 'Простите не успеваю. Давайте перенесем'
        ]);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_NOT_CONFIRM);
        expect('проверка start_at', $content['start_at'])->equals('2020-12-12 10:00:00');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/need-confirm');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 2', $content)->count(2);

        foreach ($content as $training){
            //expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_2);
            if ($training['training_uuid'] !== '1eb2fe05-341c-60d1-21f3-ccb0c31152e5'){
                continue;
            }
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
            expect('проверка status', $training['status'])->equals(TrainingSession::STATUS_NOT_CONFIRM);
            expect('проверка start_at', $training['start_at'])->equals('2020-12-12 12:00:00');
        }
    }


    public function testEmployeeRateComplete(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/planning/rate/'.TrainingSessionFixture::TRAINING_UUID_5, [
            'rate' => 8,
            'comment' => 'Спасибо за тренинг все супер.'
        ]);
        $I->seeResponseCodeIs(201);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_5);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_COMPLETED);

        $I->sendGET('/planning/for-rating');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 0', $content)->count(0);

    }

    public function testEmployeeTakeFree(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/planning/take/'.TrainingSessionFixture::TRAINING_UUID_1, [
            'link' => 'http://scype.com',
        ]);
        $I->seeResponseCodeIs(201);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_1);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_CONFIRM);

        $I->sendGET('/planning/free');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 0', $content)->count(0);

    }

    public function testEmployeeCreate(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::EMP_AUTH_10['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/planning/create',[
            'invited_uuid' => UserFixture::COACH_AUTH_4['id'],
            'subject_uuid' => SubjectFixture::SUB_4_UUID,
            'start_at' => '2020-12-01 14:00:00',
            'duration' => 3600
        ]);
        $I->seeResponseCodeIs(201);

        $content =  Json::decode($I->grabResponse());

        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_NOT_CONFIRM);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/need-confirm');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 2', $content)->count(2);

    }


    public function testCoachConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/confirmed');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
            echo 'Id: ' . $training['training_uuid'] . PHP_EOL;
            echo 'User: ' . UserFixture::COACH_AUTH_4['l'] . PHP_EOL;
            echo 'Start: ' . $training['start_at'] .  PHP_EOL;
            echo 'End: ' . $training['end_at']. PHP_EOL;
        }

    }


    public function testCoachForRating(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/for-rating');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_8);
        }
    }


    public function testCoachFree(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/free');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_1);
        }
    }

    public function testCoachWaitConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/wait-confirm');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);
        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_7);
        }

    }

    public function testCoachNeedConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/need-confirm');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_2);
        }
    }



    public function testCoachForComplete(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/for-complete');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
        }
    }


    public function testCoachCancelConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/cancel/'.TrainingSessionFixture::TRAINING_UUID_7, ['comment' => 'Простите не успеваю.']);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_7);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_CANCEL);

    }

    public function testCoachMoveConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/move/'.TrainingSessionFixture::TRAINING_UUID_3, [
            'start_at' => '2020-12-12 11:00',
            'comment' => 'Простите не успеваю. Давайте перенесем'
        ]);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_NOT_CONFIRM);
        expect('проверка start_at', $content['start_at'])->equals('2020-12-12 11:00:00');

    }

    public function testCoachMoveFree(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/move/'.TrainingSessionFixture::TRAINING_UUID_1, [
            'start_at' => '2020-12-11 11:00'
        ]);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_1);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_FREE);
        expect('проверка start_at', $content['start_at'])->equals('2020-12-11 11:00:00');

    }

    public function testCoachCancelFree(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_1['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/cancel/'.TrainingSessionFixture::TRAINING_UUID_1);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_1);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_CANCEL);
    }

    public function testCoachComplete(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/planning/rate/'.TrainingSessionFixture::TRAINING_UUID_3, [
            'rate' => 8,
            'comment' => 'Спасибо за участе, не зубудьте поставить оценку'
        ]);
        $I->seeResponseCodeIs(201);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_COMPLETED);
    }

    public function testCoachCancelNotConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/cancel/'.TrainingSessionFixture::TRAINING_UUID_2, ['comment' => 'Простите не успеваю.']);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_2);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_CANCEL);

    }

    public function testCoachConfirm(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::COACH_AUTH_4['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');
        $I->amBearerAuthenticated($token[0]);
        $I->sendPATCH('/planning/confirm/'.TrainingSessionFixture::TRAINING_UUID_2, ['link' => 'https://skype.com']);
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());

        expect('проверка uuid', $content['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_2);
        expect('проверка status', $content['status'])->equals(TrainingSession::STATUS_CONFIRM);
    }

    public function testHrConfirmed(RestTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/auth/login', [
            'login' => UserFixture::HR_AUTH_3['l'],
            'password' => UserFixture::PASSWORD
        ]);
        $token = $I->grabDataFromResponseByJsonPath('access_token');

        $I->amBearerAuthenticated($token[0]);
        $I->sendGET('/planning/index');
        $I->seeResponseCodeIs(200);

        $content =  Json::decode($I->grabResponse());
        expect('Тренинг должен быть 1', $content)->count(1);

        foreach ($content as $training){
            expect('проверка uuid', $training['training_uuid'])->equals(TrainingSessionFixture::TRAINING_UUID_3);
            echo 'Id: ' . $training['training_uuid'] . PHP_EOL;
            echo 'User: ' . UserFixture::HR_AUTH_3['l'] . PHP_EOL;
            echo 'Start: ' . $training['start_at'] .  PHP_EOL;
            echo 'End: ' . $training['end_at']. PHP_EOL;
        }

    }


}
