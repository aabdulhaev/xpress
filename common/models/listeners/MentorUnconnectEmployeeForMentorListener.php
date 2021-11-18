<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 20.04.2021
 */


namespace common\models\listeners;


use common\models\events\MentorUnconnectEmployeeForMentor;
use yii\mail\MailerInterface;

/** Сообщение коучу/ментору, когда он отключил сотрудника из своего списка (по кнопке “Отключить”). */
class MentorUnconnectEmployeeForMentorListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function handle(MentorUnconnectEmployeeForMentor $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/mentor-to-emp-unconnect-for-mentor-html', 'text' => 'contact/mentor-to-emp-unconnect-for-mentor-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->mentor->email)
                ->setSubject('Подтверждение приостановки сессий на платформе ' . \Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
