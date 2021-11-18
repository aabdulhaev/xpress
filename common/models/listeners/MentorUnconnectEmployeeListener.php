<?php

namespace common\models\listeners;

use common\access\Rbac;
use common\models\events\MentorUnconnectEmployee;
use yii\mail\MailerInterface;

/** Сообщение сотруднику, когда его отключает коуч/ментор */
class MentorUnconnectEmployeeListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(MentorUnconnectEmployee $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/mentor-to-emp-unconnect-html', 'text' => 'contact/mentor-to-emp-unconnect-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->employee->email)
                ->setSubject('Приостановка сессий на платформе ' . \Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
