<?php

namespace unit\models;

use Codeception\Test\Unit;
use common\models\Client;
use common\models\EmployeeMentor;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use common\tests\fixtures\AuthAssignmentFixture;
use common\tests\fixtures\ClientCoachFixture;
use common\tests\fixtures\ClientFixture;
use common\tests\fixtures\EmployeeMentorFixture;
use common\tests\fixtures\TrainingSessionFixture;
use common\tests\fixtures\TrainingRatingFixture;
use common\tests\fixtures\UserFixture;
use common\tests\fixtures\UserTrainingFixture;
use common\tests\UnitTester;

class TrainingTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;


    /**
     * @return array
     */
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
            //'training_rating' => TrainingRatingFixture::class
        ];
    }

    public function testEmployeeAllTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список всех тренингов сотрудника', $user->trainings)->count(7);
    }

    public function testEmployeeFreeTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getMyCoachesFreeTraining()->all();
        expect('Список свободных слотов', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_1);
    }

    public function testEmployeeConfirmedTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getConfirmedTrainings()->all();
            expect('Список запланированных тренингов сотрудника',$models) ->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_3);
    }

    public function testEmployeeNeedConfirmTrainingAnyUser(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsNotConfirm()->all();
            expect('Список запланированных тренингов сотрудника', $models)
            ->count(2);
    }

    public function testEmployeeNeedConfirmTrainingSelfUser(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsNeedSelfConfirm()->all();
        expect('Список сессий не подтвержденных сотрудником', $models)
            ->count(1);

        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_7);
    }

    public function testEmployeeNeedConfirmTrainingOtherUser(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsWaitConfirm()->all();
        expect('Список сессий не подтвержденных коучем', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_2);
    }

    public function testEmployeeCancelTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getCancelTrainings()->all();
        expect('Список отмененных сессий', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_4);
    }

    public function testEmployeeCompletedTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getCompletedTrainings()->all();
        expect('Список завершенных сессий', $models)->count(3);
    }

    public function testEmployeeNeedSelfRatingTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::EMP_AUTH_10['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsNeedSelfRating()->all();
        expect('Список завершенных сессий', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_5);
    }



    public function testCoachAllTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        expect('Список всех тренингов сотрудника', $user->trainings)->count(7);
    }

    public function testCoachConfirmedTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getConfirmedTrainings()->all();
        expect('Список запланированных тренингов сотрудника',$models) ->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_3);
    }

    public function testCoachNeedConfirmTrainingAnyUser(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsNotConfirm()->all();
        expect('Список запланированных тренингов сотрудника', $models)
            ->count(2);
    }

    public function testCoachNeedConfirmTrainingSelfUser(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsNeedSelfConfirm()->all();
        expect('Список сессий не подтвержденных сотрудником', $models)
            ->count(1);

        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_2);
    }

    public function testCoachNeedConfirmTrainingOtherUser(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainingsWaitConfirm()->all();
        expect('Список сессий не подтвержденных коучем', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_7);
    }

    public function testCoachCancelTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getCancelTrainings()->all();
        expect('Список отмененных сессий', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_4);
    }

    public function testCoachCompletedTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_4['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getCompletedTrainings()->all();
        expect('Список завершенных сессий', $models)->count(3);
    }


    public function testHrConfirmedTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::HR_AUTH_3['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getClientConfirmedTrainings()->all();
        expect('Список запланированных сессий', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_3);
    }

    public function testCoachFreeTraining(): void
    {
        $user = User::findOne(['user_uuid' => UserFixture::COACH_AUTH_1['id']]);
        expect('Пользователь найден', $user)->notEmpty();
        $models = $user->getTrainings()->andWhere(['status' => TrainingSession::STATUS_FREE])->all();
        expect('Список свободных сессий', $models)->count(1);
        expect('Проверка uuid сессии', $models[0]->training_uuid)->equals(TrainingSessionFixture::TRAINING_UUID_1);
    }

}
