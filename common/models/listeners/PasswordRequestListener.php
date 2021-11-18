<?php

namespace common\models\listeners;

use common\models\events\PasswordResetRequest;
use RuntimeException;
use Yii;
use yii\mail\MailerInterface;

class PasswordRequestListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(PasswordResetRequest $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'auth/reset/confirm-html', 'text' => 'auth/reset/confirm-text'],
                    ['user' => $event->user]
                )
                ->setTo($event->user->email)
                ->setSubject('Сброс пароля на платформе ' . Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
