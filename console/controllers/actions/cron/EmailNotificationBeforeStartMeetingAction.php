<?php

namespace console\controllers\actions\cron;

use common\dispatchers\EventDispatcher;
use common\models\events\EmailNotificationBeforeStartMeeting;
use common\models\Meeting;
use common\models\traits\EventTrait;
use common\models\UserMeeting;
use common\repositories\UserMeetingRepository;
use common\services\TransactionManager;
use yii\base\Action;
use yii\console\ExitCode;

class EmailNotificationBeforeStartMeetingAction extends Action
{
    use EventTrait;

    public $userMeetingRepository;

    private $dispatcher;

    public function __construct(
        $id,
        $controller,
        EventDispatcher $dispatcher,
        UserMeetingRepository $userMeetingRepository,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->dispatcher = $dispatcher;
        $this->userMeetingRepository = $userMeetingRepository;
    }


    /**
     * @return int
     * @throws \Exception
     */
    public function run()
    {
        $transaction = new TransactionManager();

        $transaction->wrap(function () {

            $userMeetingsQuery = UserMeeting::find()
                ->joinWith('meeting')
                ->andWhere([UserMeeting::tableName() . '.status' => UserMeeting::STATUS_CONFIRMED])
                ->andWhere([UserMeeting::tableName() . '.notify_status' => UserMeeting::NOTIFY_STATUS_CONFIRM_SEND])
                ->andWhere([Meeting::tableName() . '.status' => Meeting::STATUS_CREATE])
                ->andWhere([Meeting::tableName() . '.type' => Meeting::TYPE_GROUP_MEETING]);

            foreach ($userMeetingsQuery->each() as $userMeeting) {
                /** @var UserMeeting $userMeeting */

                /** @var Meeting $meeting */
                $meeting = $userMeeting->meeting;
                if (empty($meeting)) {
                    continue;
                }
                if (!$meeting->checkTimeToSend24Notification()) {
                    continue;
                }
                $email = !empty($userMeeting->email) ? $userMeeting->email : $userMeeting->user->email;
                if (empty($email)) {
                    continue;
                }

                $userMeeting->setNotifyStatus(UserMeeting::NOTIFY_STATUS_24_SEND);
                $this->userMeetingRepository->save($userMeeting);

                $this->recordEvent(new EmailNotificationBeforeStartMeeting($meeting, $email, $userMeeting->token));
            }

            $this->dispatcher->dispatchAll($this->events);
        });

        return ExitCode::OK;
    }
}
