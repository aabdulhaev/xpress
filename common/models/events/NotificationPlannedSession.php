<?php

namespace common\models\events;

use common\models\TrainingSession;
use common\models\User;

class NotificationPlannedSession
{
    public $user;
    public $training;

    public function __construct(User $user, TrainingSession $training)
    {
        $this->user = $user;
        $this->training = $training;
    }
}
