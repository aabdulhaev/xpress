<?php

/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link http://good-master.com.ua/
 * Creator: VadimTs
 * Date: 26.04.2021
 */

namespace common\models\listeners;

use common\models\events\MoveSessionRequest;
use common\models\events\RejectedMoveSessionRequest;
use yii\mail\MailerInterface;

class RejectedMoveSessionRequestListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(RejectedMoveSessionRequest $event): bool
    {
        try {
            $role = $event->sender->isUserRoleCoach() ? 'Коуч' : 'Ментор';

            return $this->mailer
                ->compose(
                    [
                        'html' => 'meeting/rejected-move-session-request-html',
                        'text' => 'meeting/rejected-move-session-request-text'
                    ],
                    [
                        'session' => $event->session,
                        'sender' => $event->sender,
                        'recipient' => $event->recipient,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->recipient->email)
                ->setSubject($role . ' не может подтвердить перенос сессии на платформе ' . \Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error(
                $exception->getMessage() . "\n" . $exception->getFile() .
                "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString()
            );
            throw $exception;
        }
    }
}
