<?php

namespace common\models\events;

use common\models\Meeting;

class EmailNotificationAtStartMeeting
{
    public $meeting;
    public $email;
    public $token;

    public function __construct(Meeting $meeting, string $email, string $token)
    {
        $this->meeting = $meeting;
        $this->email = $email;
        $this->token = $token;
    }
}
