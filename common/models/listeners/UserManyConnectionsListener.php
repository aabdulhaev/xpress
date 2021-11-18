<?php

namespace common\models\listeners;

use common\access\Rbac;
use common\models\events\UserManyConnections;
use yii\mail\MailerInterface;

class UserManyConnectionsListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserManyConnections $event): void
    {
        $role = $event->mentor->role === Rbac::ROLE_MENTOR ? 'менторы' : 'коучи';

        try {
            $this->mailer
                ->compose(
                    [
                        'html' => 'connect/emp-to-many-mentor-html',
                        'text' => 'connect/emp-to-many-mentor-text'
                    ],
                    [
                        'employee' => $event->employee,
                        'mentorsRole' => $event->mentor->role
                    ]
                )
                ->setTo($event->employee->email)
                ->setSubject('Вам назначены ' . $role . ' на платформе ' . \Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error(
                $exception->getMessage() . "\n" .
                $exception->getFile() . "(" . $exception->getLine() . ")\n" .
                $exception->getTraceAsString()
            );
            throw $exception;
        }
    }
}
