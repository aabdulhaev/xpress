<?php

namespace common\models\events;

use common\models\User;

class UserSignUpRequested
{
    public $user;
    public $pwd;

    public function __construct(User $user, string $pwd)
    {
        $this->user = $user;
        $this->pwd = $pwd;
    }
}
