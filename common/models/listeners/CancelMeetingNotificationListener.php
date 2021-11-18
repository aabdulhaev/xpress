<?php

namespace common\models\listeners;


use common\models\events\CancelMeetingNotification;
use yii\mail\MailerInterface;

class CancelMeetingNotificationListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(CancelMeetingNotification $event): bool
    {
        try {
            return $this->mailer
                ->compose(
                    [
                        'html' => 'meeting/cancel-meeting-notification-html',
                        'text' => 'meeting/cancel-meeting-notification-text'
                    ],
                    [
                        'meeting' => $event->meeting,
                    ]
                )
                ->setTo($event->meeting->getParticipantsEmails())
                ->setSubject("Отмена вебинара на платформе ".\Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
