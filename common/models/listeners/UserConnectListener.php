<?php

namespace common\models\listeners;

use common\access\Rbac;
use common\models\events\UserConnect;
use RuntimeException;
use yii\mail\MailerInterface;

class UserConnectListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserConnect $event): void
    {
        try {
            $this->mentorToEmpMail($event);
            $this->empToMentorMail($event);
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }

    protected function empToMentorMail(UserConnect $event): void
    {
        $role = $event->mentor->role === Rbac::ROLE_MENTOR ? 'ментор' : 'коуч';
        $this->mailer
            ->compose(
                ['html' => 'connect/emp-to-mentor-html', 'text' => 'connect/emp-to-mentor-text'],
                [
                    'employee' => $event->employee,
                    'mentor' => $event->mentor
                ]
            )
            ->setTo($event->employee->email)
            ->setSubject('Вам назначен '. $role .' на платформе ' . \Yii::$app->name)
            ->send();
    }

    protected function mentorToEmpMail(UserConnect $event): void
    {
        $role = $event->mentor->role === Rbac::ROLE_MENTOR ? 'менти' : 'коучи';
        $this->mailer
            ->compose(
                ['html' => 'connect/mentor-to-emp-html', 'text' => 'connect/mentor-to-emp-text'],
                [
                    'employee' => $event->employee,
                    'mentor' => $event->mentor
                ]
            )
            ->setTo($event->mentor->email)
            ->setSubject('Вам назначен '. $role .' на платформе ' . \Yii::$app->name)
            ->send();
    }
}
