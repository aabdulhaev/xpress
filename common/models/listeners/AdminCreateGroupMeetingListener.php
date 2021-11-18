<?php

namespace common\models\listeners;

use common\models\events\AdminCreateGroupMeeting;
use common\models\UserMeeting;
use common\repositories\UserMeetingRepository;
use yii\db\ActiveQuery;
use yii\mail\MailerInterface;

class AdminCreateGroupMeetingListener
{
    private $mailer;
    public $service;
    public $repository;

    public function __construct(MailerInterface $mailer, UserMeetingRepository $repository)
    {
        $this->mailer = $mailer;
        $this->repository = $repository;
    }

    public function handle(AdminCreateGroupMeeting $event): void
    {
        /** @var ActiveQuery $query */
        $query = $event->meeting->prepareNotInvitedUsersQuery();

        foreach ($query->each() as $userMeeting) {
            /** @var UserMeeting $userMeeting */
            try {
                $email = !empty($userMeeting->email) ? $userMeeting->email : $userMeeting->user->email;
                if (empty($email)) {
                    continue;
                }

                $this->mailer
                    ->compose(
                        ['html' => 'meeting/admin-create-group-meeting-html', 'text' => 'meeting/admin-create-group-meeting-txt'],
                        [
                            'meeting' => $event->meeting,
                            'token' => $userMeeting->token
                        ]
                    )
                    ->setTo($email)
                    ->setSubject("Приглашение на вебинар на платформе EMPLITUDE")
                    ->send();

                $userMeeting->setNotifyStatus(UserMeeting::NOTIFY_STATUS_CONFIRM_SEND);
                $this->repository->save($userMeeting);
            } catch (\RuntimeException $exception) {
                \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
                throw $exception;
            }
        }
    }
}
