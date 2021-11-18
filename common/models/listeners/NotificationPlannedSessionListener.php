<?php

namespace common\models\listeners;

use common\models\events\NotificationPlannedSession;
use yii\mail\MailerInterface;

class NotificationPlannedSessionListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(NotificationPlannedSession $event): void
    {
        try {
            $this->mailer
                ->compose(
                    [
                        'html' => 'notify/planned-session-notification-html',
                        'text' => 'notify/planned-session-notification-text'
                    ],
                    [
                        'recipient' => $event->user,
                        'coach' => $event->training->coachOrMentor,
                        'training' => $event->training
                    ]
                )
                ->setTo($event->user->email)
                ->setSubject('Напоминание о сессии на платформе ' . \Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
