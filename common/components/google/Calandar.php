<?php

namespace common\components\google;

use Google\Service\Calendar\Event;
use Google_Client;
use Google_Service_Calendar;

class Calandar
{
    private $calendarId = 'primary';

    /**
     * @var Google_Service_Calendar
     */
    private $service;

    public function __construct(Google_Client $client, Token $accessToken)
    {
        $client->setAccessToken($accessToken->getAccessToken());
        $this->service = new Google_Service_Calendar($client);
    }

    public function addEvent(EventGenerator $eventGenerator): Event
    {
        return $this->service->events->insert($this->calendarId, $eventGenerator->generate());
    }

    public function editEvent(string $eventId, EventGenerator $eventGenerator): Event
    {
        $this->service->events->delete($this->calendarId, $eventId);
        return $this->service->events->insert($this->calendarId, $eventGenerator->generate());
    }

    public function removeEvent(string $eventId): void
    {
        $this->service->events->delete($this->calendarId, $eventId);
    }
}