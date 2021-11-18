<?php

namespace common\models\listeners;

use common\models\events\EmployeeUnconnectMentor;
use common\models\events\MentorUnconnectEmployee;
use RuntimeException;
use Yii;
use yii\mail\MailerInterface;

/** Сообщение коучу, когда его отключает сотрудник из своего списка (по кнопке “Отключить”). */
class EmployeeUnconnectMentorListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EmployeeUnconnectMentor $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/emp-to-mentor-unconnect-html', 'text' => 'contact/emp-to-mentor-unconnect-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->mentor->email)
                ->setSubject('Приостановка сессий на платформе ' . Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
