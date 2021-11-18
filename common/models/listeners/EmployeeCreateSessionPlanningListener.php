<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 20.04.2021
 */


namespace common\models\listeners;


use common\models\events\EmployeeCreateSessionPlanning;
use yii\mail\MailerInterface;

class EmployeeCreateSessionPlanningListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EmployeeCreateSessionPlanning $event): void
    {
        try {$this->mailer
            ->compose(
                [
                    'html' => 'connect/employee-create-session-planning-html',
                    'text' => 'connect/employee-create-session-planning-text'
                ],
                [
                    'employee' => $event->employee,
                    'mentor' => $event->mentor,
                    'dateStart' => $event->dateStart
                ]
            )
            ->setTo($event->mentor->email)
            ->setSubject('У вас запрос на сессию на платформе ' . \Yii::$app->name)
            ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
