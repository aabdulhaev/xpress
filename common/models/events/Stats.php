<?php

namespace common\models\events;

use common\models\SessionRating;

class Stats
{
    public $sessionRating;

    public function __construct(SessionRating $assigment)
    {
        $this->sessionRating = $assigment;
    }
}
