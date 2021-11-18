<?php

namespace common\models\listeners;

use common\access\Rbac;
use common\models\events\MentorCancelEmployee;
use common\models\events\MentorUnconnectEmployee;
use yii\mail\MailerInterface;

/** Сообщение сотруднику, когда его отключает коуч/ментор */
class MentorCancelEmployeeListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(MentorCancelEmployee $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/mentor-to-emp-cancel-html', 'text' => 'contact/mentor-to-emp-cancel-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->employee->email)
                ->setSubject('Ваш запрос на сотрудничество на платформе '. \Yii::$app->name .' отклонен')
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
