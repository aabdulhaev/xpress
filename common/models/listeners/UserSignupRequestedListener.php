<?php

namespace common\models\listeners;

use common\models\events\UserSignUpRequested;
use RuntimeException;
use yii\console\Exception;
use yii\mail\MailerInterface;

class UserSignupRequestedListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserSignUpRequested $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'auth/signup/confirm-html', 'text' => 'auth/signup/confirm-text'],
                    ['user' => $event->user, 'pwd' => $event->pwd]
                )
                ->setTo($event->user->email)
                ->setSubject('Добро пожаловать на платформу ' . \Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
