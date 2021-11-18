<?php

namespace common\components\google;

use DateTimeImmutable;
use DateTimeZone;
use Google_Service_Calendar_Event;

class EventGenerator
{
    /**
     * @var string
     */
    private $summary;
    /**
     * @var string
     */
    private $description;
    /**
     * @var \DateTimeImmutable
     */
    private $startDateTime;
    /**
     * @var \DateTimeImmutable
     */
    private $endDateTime;
    /**
     * @var array
     */
    private $attendees;

    public function __construct(
        string $summary,
        string $description,
        DateTimeZone $timeZone,
        DateTimeImmutable $startDateTime,
        DateTimeImmutable $endDateTime,
        array $attendeesList
    ) {
        $this->summary = $summary;
        $this->description = $description;
        $this->timeZone = $timeZone;
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->attendees = $attendeesList;
    }

    public function generate(): Google_Service_Calendar_Event
    {
        return new Google_Service_Calendar_Event(
            [
                'summary' => $this->summary,
                'description' => $this->description,
                'start' => [
                    'dateTime' => $this->startDateTime->format('c'),
                    'timeZone' => $this->timeZone->getName(),
                ],
                'end' => [
                    'dateTime' => $this->endDateTime->format('c'),
                    'timeZone' => $this->timeZone->getName(),
                ],
                'attendees' => $this->getAttendees(),
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 24 * 60],
                        ['method' => 'popup', 'minutes' => 10],
                    ],
                ],
            ]
        );
    }

    private function getAttendees(): array
    {
        $attendees = [];
        foreach ($this->attendees as $attendee) {
            $attendees[] = [
                'email' => $attendee
            ];
        }
        return $attendees;
    }
}