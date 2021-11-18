<?php

namespace common\models\events;

use common\models\User;

class PasswordResetRequest
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
