<?php

namespace common\models\listeners;

use common\models\events\EmailNotificationAtStartMeeting;
use yii\mail\MailerInterface;

class EmailNotificationAtStartMeetingListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EmailNotificationAtStartMeeting $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'meeting/email-notification-at-start-meeting-html', 'text' => 'meeting/email-notification-at-start-meeting-text'],
                    [
                        'meeting' => $event->meeting,
                        'token' => $event->token,
                    ]
                )
                ->setTo($event->email)
                ->setSubject("Вебинар на платформе EMPLITUDE начался")
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
