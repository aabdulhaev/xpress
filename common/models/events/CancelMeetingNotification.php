<?php

namespace common\models\events;


use common\models\Meeting;

class CancelMeetingNotification
{
    public $meeting;

    /**
     * CancelMeetingNotification constructor.
     * @param Meeting $meeting
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }
}
