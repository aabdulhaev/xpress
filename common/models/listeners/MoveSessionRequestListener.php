<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link http://good-master.com.ua/
 * Creator: VadimTs
 * Date: 26.04.2021
 */

namespace common\models\listeners;


use common\models\events\MoveSessionRequest;
use yii\mail\MailerInterface;

class MoveSessionRequestListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(MoveSessionRequest $event): bool
    {
        try {
            return $this->mailer
                ->compose(
                    ['html' => 'meeting/move-session-request-html', 'text' => 'meeting/move-session-request-text'],
                    [
                        'session' => $event->session,
                        'sender' => $event->sender,
                        'recipient' => $event->recipient,
                        'comment' => $event->comment,
                        'fromDateTime' => $event->fromDateTime,
                        'toDateTime' => $event->toDateTime,
                    ]
                )
                ->setTo($event->recipient->email)
                ->setSubject("Запрос на перенос сессии на платформе ".\Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
