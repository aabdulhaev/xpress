<?php

namespace common\models\events;

use common\models\TrainingSession;

class MentorCreateMeeting
{
    public $employee;
    public $mentor;

    public function __construct(TrainingSession $assigment)
    {
        $this->employee = $assigment->employee;
        $this->mentor = $assigment->coachOrMentor;
    }
}
