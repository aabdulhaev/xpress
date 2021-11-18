<?php

namespace common\models\events;


use common\models\Meeting;
use common\models\UserMeeting;

class MoveMeetingNotification
{
    public $meeting;
    public $userMeeting;
    public $previousStartDate;
    public $previousStartTime;

    /**
     * MoveMeetingNotification constructor.
     * @param Meeting $meeting
     * @param UserMeeting $userMeeting
     * @param string $previousStartDate
     * @param string $previousStartTime
     */
    public function __construct(Meeting $meeting, UserMeeting $userMeeting, string $previousStartDate, string $previousStartTime)
    {
        $this->meeting = $meeting;
        $this->userMeeting = $userMeeting;
        $this->previousStartDate = $previousStartDate;
        $this->previousStartTime = $previousStartTime;
    }
}
