<?php

namespace common\models\events;

use common\models\TrainingSession;

class CancelSession
{
    public $session;

    public function __construct(TrainingSession $session)
    {
        $this->session = $session;
    }
}
