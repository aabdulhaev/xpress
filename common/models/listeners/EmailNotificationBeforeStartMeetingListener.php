<?php

namespace common\models\listeners;

use common\models\events\EmailNotificationBeforeStartMeeting;
use yii\mail\MailerInterface;

class EmailNotificationBeforeStartMeetingListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EmailNotificationBeforeStartMeeting $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'meeting/email-notification-before-start-meeting-html', 'text' => 'meeting/email-notification-before-start-meeting-text'],
                    [
                        'meeting' => $event->meeting,
                        'token' => $event->token,
                    ]
                )
                ->setTo($event->email)
                ->setSubject("Ссылка для подключения к вебинару на платформе EMPLITUDE")
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
