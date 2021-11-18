<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 22.04.2021
 */


namespace common\models\listeners;


use common\models\events\ConfirmSession;
use Yii;
use yii\mail\MailerInterface;

class ConfirmSessionListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(ConfirmSession $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'meeting/confirm-session-html', 'text' => 'meeting/confirm-session-text'],
                    [
                        'session' => $event->session,
                        'sender' => $event->sender,
                        'recipient' => $event->recipient
                    ]
                )
                ->setTo($event->recipient->email)
                ->setSubject("Сессия на платформе ".Yii::$app->name." подтверждена")
                ->send();

            $this->mailer
                ->compose(
                    ['html' => 'meeting/confirm-session-html', 'text' => 'meeting/confirm-session-text'],
                    [
                        'session' => $event->session,
                        'sender' => $event->recipient,
                        'recipient' => $event->sender
                    ]
                )
                ->setTo($event->sender->email)
                ->setSubject("Сессия на платформе ".Yii::$app->name." подтверждена")
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
