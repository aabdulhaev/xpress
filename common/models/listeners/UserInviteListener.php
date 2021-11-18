<?php

namespace common\models\listeners;

use common\models\events\UserInvite;
use RuntimeException;
use Yii;
use yii\mail\MailerInterface;

class UserInviteListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserInvite $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/hr-to-emp-html', 'text' => 'contact/hr-to-emp-text'],
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
