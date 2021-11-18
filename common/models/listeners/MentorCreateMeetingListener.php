<?php

namespace common\models\listeners;

use common\access\Rbac;
use common\models\events\MentorCreateMeeting;
use yii\mail\MailerInterface;

class MentorCreateMeetingListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(MentorCreateMeeting $event): void
    {

        $role = Rbac::rolesTitle()[$event->mentor->role];

        try {
            $this->mailer
                ->compose(
                    ['html' => 'meeting/mentor-create-meeting-html', 'text' => 'meeting/mentor-create-meeting-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'role' => $role
                    ]
                )
                ->setTo($event->employee->email)
                ->setSubject("Ваш {$role} ожидает Вас на сессии")
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
