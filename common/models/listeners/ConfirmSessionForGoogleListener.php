<?php

namespace common\models\listeners;

use common\components\Google;
use common\components\google\EventGenerator;
use common\models\events\ConfirmSession;
use common\models\TrainingSession;
use common\models\User;
use common\models\UserTraining;
use common\useCases\UserManageCase;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use Google\Service\Calendar\Event;
use Yii;

class ConfirmSessionForGoogleListener
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

    public function handle(ConfirmSession $event): void
    {
        $session = $event->session;
        $coachOrMentor = $this->getCoachOrMentor($session);
        $employee = $this->getEmployee($session);
        $userTraining = $this->getUserTraining($coachOrMentor, $session);

        $googleCalendarEvent = $this->googleCommand($session, $coachOrMentor, $employee, $userTraining);

        //todo сделать через command
        $userTraining->google_event_id = $googleCalendarEvent->getId();
        $userTraining->save(false);
    }

    private function googleCommand(
        TrainingSession $session,
        User $coachOrMentor,
        User $employee,
        UserTraining $userTraining
    ): Event {
        $eventGenerator = $this->getEventGenerator($session, $coachOrMentor, $employee);
        $this->manageCase->verifyAndUpdateGoogleToken($coachOrMentor);

        $calendar = $this->google->getCalendar($coachOrMentor->getGoogleAccessToken());
        return $userTraining->google_event_id === null
            ? $calendar->addEvent($eventGenerator)
            : $calendar->editEvent($userTraining->google_event_id, $eventGenerator);
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

    private function getEmployee(TrainingSession $session): User
    {
        /** @var User $employee */
        $employee = $session->getEmployee()->one();
        if ($employee === null) {
            throw new DomainException('Employee not found');
        }

        return $employee;
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

        return $userTraining;
    }

    private function getEventGenerator(TrainingSession $session, User $coachOrMentor, User $employee): EventGenerator
    {
        return new EventGenerator(
            "Сессия XPress, $employee->last_name",
            "Сессия XPress с сотрудником $employee->first_name $employee->last_name",
            new DateTimeZone($coachOrMentor->time_zone),
            new DateTimeImmutable($session->start_at_tc),
            (new DateTimeImmutable($session->start_at_tc))->add(new DateInterval("PT{$session->duration}S")),
            [$coachOrMentor->email]
        );
    }
}
