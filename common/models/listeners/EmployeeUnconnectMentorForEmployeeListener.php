<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 20.04.2021
 */


namespace common\models\listeners;


use common\models\events\EmployeeUnconnectMentorForEmployee;
use yii\mail\MailerInterface;

/** Сообщение сотруднику, когда он отключил коуча/ментора из своего списка (по кнопке “Отключить”). */
class EmployeeUnconnectMentorForEmployeeListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EmployeeUnconnectMentorForEmployee $event): void
    {
        try {$this->mailer
            ->compose(
                ['html' => 'contact/emp-to-mentor-unconnect-for-emp-html', 'text' => 'contact/emp-to-mentor-unconnect-for-emp-text'],
                [
                    'employee' => $event->employee,
                    'mentor' => $event->mentor,
                    'comment' => $event->comment
                ]
            )
            ->setTo($event->employee->email)
            ->setSubject('Подтверждение приостановки сессий на платформе ' . \Yii::$app->name)
            ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
