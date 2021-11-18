<?php

namespace common\models\listeners;

use common\access\Rbac;
use common\models\events\EmployeeCreateConnect;
use RuntimeException;
use Yii;
use yii\mail\MailerInterface;

class EmployeeCreateConnectListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EmployeeCreateConnect $event): void
    {
        try {
            $this->mailer
                ->compose(
                    ['html' => 'contact/emp-to-mentor-connect-html', 'text' => 'contact/emp-to-mentor-connect-text'],
                    [
                        'employee' => $event->employee,
                        'mentor' => $event->mentor,
                        'comment' => $event->comment
                    ]
                )
                ->setTo($event->mentor->email)
                ->setSubject("Вас приглашают к сотрудничеству на платформе " . Yii::$app->name)
                ->send();
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }
}
