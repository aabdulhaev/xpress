<?php

namespace common\models\listeners;

use common\components\Google;
use common\models\events\CancelSession;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use common\useCases\UserManageCase;
use DomainException;
use Yii;

class CancelSessionForGoogleListener
{
    /** @var Google */
    private $google;

    /** @var UserManageCase */
    private $manageCase;

    public function __construct(UserManageCase $manageCase)
    {
        $this->google = Yii::$app->google;
        $this->manageCase = $manageCase;
    }

    public function handle(CancelSession $event): void
    {
        $session = $event->session;
        $coachOrMentor = $this->getCoachOrMentor($session);
        $userTraining = $this->getUserTraining($coachOrMentor, $session);

        $this->manageCase->verifyAndUpdateGoogleToken($coachOrMentor);

        $this
            ->google
            ->getCalendar($coachOrMentor->getGoogleAccessToken())
            ->removeEvent($userTraining->google_event_id);

        //todo сделать через command
        $userTraining->google_event_id = null;
        $userTraining->save(false);
    }

    private function getCoachOrMentor(TrainingSession $session): User
    {
        /** @var User $coachOrMentor */
        $coachOrMentor = $session->getCoachOrMentor()->one();
        if ($coachOrMentor === null) {
            throw new DomainException('Coach or mentor not found');
        }

        if (!$coachOrMentor->getGoogleAccessToken()->issetAccessToken()) {
            throw new DomainException('Google profile not connected');
        }

        return $coachOrMentor;
    }

    private function getUserTraining(User $coachOrMentor, TrainingSession $session): UserTraining
    {
        $userTraining = UserTraining::findOne(
            [
                'user_uuid' => $coachOrMentor->user_uuid,
                'training_uuid' => $session->training_uuid
            ]
        );

        if ($userTraining === null) {
            throw new DomainException('User training not found');
        }

        if ($userTraining->google_event_id === null) {
            throw new DomainException('There is no such event in google calendar');
        }

        return $userTraining;
    }
}
