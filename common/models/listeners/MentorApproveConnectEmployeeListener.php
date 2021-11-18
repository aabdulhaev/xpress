<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 19.04.2021
 */


namespace common\models\listeners;


use common\models\events\MentorApproveConnectEmployee;
use yii\mail\MailerInterface;

/** Сообщение сотруднику, когда коуч одобряет заявку (по кнопке “Принять”) */
class MentorApproveConnectEmployeeListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(MentorApproveConnectEmployee $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'connect/mentor-approve-emp-html', 'text' => 'connect/mentor-approve-emp-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->employee->email)
                ->setSubject('Ваш запрос на сотрудничество на платформе ' . \Yii::$app->name.' принят')
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
