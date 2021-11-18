<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link http://good-master.com.ua/
 * Creator: VadimTs
 * Date: 26.04.2021
 */

namespace common\models\listeners;


use common\models\events\CancelSessionNotification;
use yii\mail\MailerInterface;

class CancelSessionNotificationListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(CancelSessionNotification $event): bool
    {
        try {
            return $this->mailer
                ->compose(
                    [
                        'html' => 'meeting/cancel-session-notification-html',
                        'text' => 'meeting/cancel-session-notification-text'
                    ],
                    [
                        'session' => $event->session,
                        'sender' => $event->sender,
                        'recipient' => $event->recipient,
                        'comment' => $event->comment,
                    ]
                )
                ->setTo($event->recipient->email)
                ->setSubject("Отмена сессии на платформе ".\Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
