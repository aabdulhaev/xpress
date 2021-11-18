<?php

namespace console\controllers\actions\cron;

use common\dispatchers\EventDispatcher;
use common\models\events\EmailNotificationAtStartMeeting;
use common\models\Meeting;
use common\models\traits\EventTrait;
use common\models\User;
use common\models\UserMeeting;
use common\repositories\MeetingRepository;
use common\repositories\UserMeetingRepository;
use common\repositories\UserRepository;
use common\services\TransactionManager;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\ActiveQuery;

class EmailNotificationAtStartMeetingAction extends Action
{
    use EventTrait;

    public $userMeetingRepository;
    public $meetingRepository;
    public $userRepository;

    private $dispatcher;

    public function __construct(
        $id,
        $controller,
        EventDispatcher $dispatcher,
        UserMeetingRepository $userMeetingRepository,
        MeetingRepository $meetingRepository,
        UserRepository $userRepository,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->dispatcher = $dispatcher;
        $this->userMeetingRepository = $userMeetingRepository;
        $this->meetingRepository = $meetingRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * @return int
     * @throws \Exception
     */
    public function run()
    {
        $transaction = new TransactionManager();

        $transaction->wrap(function () {
            /** @var ActiveQuery $userMeetingsQuery */
            $userMeetingsQuery = $this->prepareQuery();
            $this->addToQueue($userMeetingsQuery);
            $this->dispatcher->dispatchAll($this->events);
        });

        return ExitCode::OK;
    }

    /**
     * @return ActiveQuery
     */
    private function prepareQuery(): ActiveQuery
    {
        return UserMeeting::find()
            ->andWhere([
                'in',
                UserMeeting::tableName() . '.status',
                [UserMeeting::STATUS_CONFIRMED, UserMeeting::STATUS_JOINED]
            ])
            ->andWhere([
                'or',
                [UserMeeting::tableName() . '.notify_status' => UserMeeting::NOTIFY_STATUS_CONFIRM_SEND],
                [UserMeeting::tableName() . '.notify_status' => UserMeeting::NOTIFY_STATUS_24_SEND]
            ])
            ->joinWith('meeting')
            ->andWhere(['!=', Meeting::tableName() . '.status', Meeting::STATUS_DELETED])
            ->andWhere([Meeting::tableName() . '.type' => Meeting::TYPE_GROUP_MEETING]);
    }

    /**
     * @param ActiveQuery $userMeetingsQuery
     * @throws \yii\base\InvalidConfigException
     */
    private function addToQueue(ActiveQuery $userMeetingsQuery)
    {
        foreach ($userMeetingsQuery->each() as $userMeeting) {
            /** @var UserMeeting $userMeeting */

            /** @var Meeting $meeting */
            $meeting = $userMeeting->meeting;
            if (empty($meeting)) {
                continue;
            }
            /** @var User $user */
            $user = $userMeeting->user;

            $email = !empty($user) ? $user->email : $userMeeting->email;
            
            if (!empty($user) && ($user->isUserRoleAdmin() || $user->isUserModerator())) {
                if (!$meeting->checkStartTimeToJoin()) {
                    continue;
                }
            } else {
                if (!$meeting->checkTimeToStartSendNotification()) {
                    continue;
                }
                if ($meeting->isStatusCreated()) {
                    /** @var User $createdUser */
                    $createdUser = $this->userRepository->getByUuid([$meeting->created_by])->one();
                    if (empty($createdUser)) {
                        continue;
                    }
                    /** @var UserMeeting $createdUserMeeting */
                    $createdUserMeeting = $this->userMeetingRepository->getByMeetingAndUser($meeting->meeting_uuid, $createdUser->user_uuid);
                    if ($createdUserMeeting->isJoined()) {
                        $meeting->setStatus(Meeting::STATUS_STARTED);
                        $this->meetingRepository->save($meeting);
                    } else {
                        continue;
                    }
                }
            }

            $userMeeting->setNotifyStatus(UserMeeting::NOTIFY_STATUS_START_SEND);
            $this->userMeetingRepository->save($userMeeting);

            $this->recordEvent(new EmailNotificationAtStartMeeting($meeting, $email, $userMeeting->token));
        }
    }
}
