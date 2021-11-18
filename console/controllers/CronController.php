<?php

namespace console\controllers;

use common\access\Rbac;
use common\components\Google;
use common\models\EmployeeMentor;
use common\models\Program;
use common\models\User;
use common\models\UserProgram;
use common\models\UserTraining;
use common\services\MeetingManager;
use common\useCases\UserManageCase;
use console\controllers\actions\cron\CancelExpiredSessionAction;
use console\controllers\actions\cron\CheckFreeSessionAction;
use console\controllers\actions\cron\EmailNotificationAtStartMeetingAction;
use console\controllers\actions\cron\EmailNotificationBeforeStartMeetingAction;
use console\controllers\actions\cron\NotificationPlannedSessionAction;
use DomainException;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class CronController extends Controller
{
    public $service;

    private $manageCase;

    public function __construct(
        $id,
        $module,
        MeetingManager $service,
        UserManageCase $manageCase,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->manageCase = $manageCase;
    }

    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'check-free-session' => CheckFreeSessionAction::class,
                'cancel-expired-session' => CancelExpiredSessionAction::class,
                'notification-planned-session' => NotificationPlannedSessionAction::class,
                'email-notification-before-start-meeting' => EmailNotificationBeforeStartMeetingAction::class,
                'email-notification-at-start-meeting' => EmailNotificationAtStartMeetingAction::class
            ]
        );
    }

    /**
     * TODO добавить описание и проверить необходимость в cron запросах
     */
    public function actionClearOld(): void
    {
        $deleted = EmployeeMentor::deleteAll(
            'status = :status AND updated_at < :time',
            [
                ':status' => EmployeeMentor::STATUS_DECLINE,
                ':time' => time() - Yii::$app->params['clearDeclineTime']
            ]
        );

        Console::output("Очищенно {$deleted} записей.");
    }

    /**
     * TODO добавить описание и проверить необходимость в cron запросах
     */
    public function actionRegisterHook()
    {
        try {
            $resp = $this->service->createHook();
            Console::output(var_export($resp));
        } catch (DomainException $e) {
            Console::output($e->getMessage());
        }
    }

    /**
     * TODO похоже это было добавлено просто для привязки через консоль, нужно будет вынести
     * в более подходящее место, это не крон задача
     */
    public function actionAddProgram()
    {
        $coaches = User::find()->andWhere(['role' => Rbac::ROLE_COACH]);

        foreach ($coaches->each() as $user) {
            /**
             * @var $user User
             */
            if (!$user->getPrograms()->andWhere(['program_uuid' => Program::COACH_UUID])->exists()) {
                $program = new UserProgram([
                    'program_uuid' => Program::COACH_UUID,
                    'user_uuid' => $user->user_uuid,
                    'created_by' => User::SEED_ADMIN_UUID,
                ]);
                $program->detachBehavior('user');
                if (!$program->save()) {
                    var_export($program->errors);
                }
            }
        }

        $mentors = User::find()->andWhere(['role' => Rbac::ROLE_MENTOR]);

        foreach ($mentors->each() as $user) {
            /**
             * @var $user User
             */
            if (!$user->getPrograms()->andWhere(['program_uuid' => Program::MENTOR_UUID])->exists()) {
                $program = new UserProgram([
                    'program_uuid' => Program::MENTOR_UUID,
                    'user_uuid' => $user->user_uuid,
                    'created_by' => User::SEED_ADMIN_UUID,
                ]);
                $program->detachBehavior('user');
                if (!$program->save()) {
                    var_export($program->errors);
                }
            }
        }
    }

    public function actionRemoveOldGoogleEvents(): void
    {
        $usersTraining = UserTraining::find()
            ->with('user')
            ->where('google_event_id IS NOT NULL')
            ->andWhere(['status' => [UserTraining::STATUS_CANCEL, UserTraining::STATUS_DELETED]]);
        /** @var Google $google */
        $google = Yii::$app->google;
        /** @var UserTraining $userTraining */
        foreach ($usersTraining->each() as $userTraining) {
            $user = $userTraining->user;
            $this->manageCase->verifyAndUpdateGoogleToken($user);
            $calendar = $google->getCalendar($user->getGoogleAccessToken());
            try {
                $calendar->removeEvent($userTraining->google_event_id);
                $userTraining->google_event_id = null;
                $userTraining->save(false);
            } catch (Exception $exception) {
                Yii::error($exception->getMessage());
            }
        }
    }
}
