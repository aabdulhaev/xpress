<?php

namespace common\models\listeners;


use common\models\events\MoveMeetingNotification;
use yii\mail\MailerInterface;

class MoveMeetingNotificationListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(MoveMeetingNotification $event): bool
    {
        $email = !empty($event->userMeeting->email) ? $event->userMeeting->email : $event->userMeeting->user->email;

        try {
            return $this->mailer
                ->compose(
                    [
                        'html' => 'meeting/move-meeting-notification-html',
                        'text' => 'meeting/move-meeting-notification-text'
                    ],
                    [
                        'meeting' => $event->meeting,
                        'userMeeting' => $event->userMeeting,
                        'previousStartDate' => $event->previousStartDate,
                        'previousStartTime' => $event->previousStartTime
                    ]
                )
                ->setTo($email)
                ->setSubject("Изменение даты/времени начала вебинара на платформе ".\Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
