<?php

namespace common\models\listeners;

use common\models\events\UserContact;
use RuntimeException;
use Yii;
use yii\mail\MailerInterface;

class UserContactListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserContact $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/mentor-to-emp-html', 'text' => 'contact/mentor-to-emp-text'],
                    [
                        'userFrom' => $event->userFrom,
                        'userTo' => $event->userTo,
                        'body' => $event->body
                    ]
                )
                ->setTo($event->userTo->email)
                ->setSubject('У вас новое сообщение на платформе ' . Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
