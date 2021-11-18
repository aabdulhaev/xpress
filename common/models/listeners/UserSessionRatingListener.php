<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 05.05.2021
 */


namespace common\models\listeners;


use common\access\Rbac;
use common\models\events\UserSessionRating;
use common\models\User;
use yii\mail\MailerInterface;

class UserSessionRatingListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserSessionRating $event): void
    {
        try {
            $this->sendRatingEmail($event);
        } catch (\RuntimeException $exception) {
            \Yii::error($exception->getMessage() . "\n" . $exception->getFile() . "(" . $exception->getLine() . ")\n" . $exception->getTraceAsString());
            throw $exception;
        }
    }

    /**
     * @param $event UserSessionRating
     * @return bool
     */
    public function sendRatingEmail($event): void
    {
        $this->mailer
            ->compose(
                $this->getViewsTemplate($event->sender),
                [
                    'session' => $event->session,
                    'sender' => $event->sender,
                    'recipient' => $event->recipient,
                    'comment' => $event->comment,
                    'started_at' => $event->started_at,
                    'subjects' => $event->subjects,
                    'rate' => $event->rate,
                ]
            )
            ->setTo(\Yii::$app->params['supportEmail'])
            ->setSubject('Оценка сессии на платформе ' . \Yii::$app->name)
            ->send();
    }

    /**
     * @param $user User
     */
    protected function getViewsTemplate($user): array
    {
        if($user->role === Rbac::ROLE_COACH){
            return ['html' => 'meeting/coach-to-employee-session-rating-html', 'text' => 'meeting/coach-to-employee-session-rating-text'];
        }

        return ['html' => 'meeting/user-session-rating-html', 'text' => 'meeting/user-session-rating-text'];

    }

}
