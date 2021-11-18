<?php

namespace common\models\events;

use common\models\Meeting;

class AdminCreateGroupMeeting
{
    public $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }
}
