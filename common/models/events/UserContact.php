<?php

namespace common\models\events;

use common\models\User;

class UserContact
{
    public $userFrom;
    public $userTo;
    public $body;

    public function __construct(User $userFrom, User $userTo, string $body)
    {
        $this->userFrom = $userFrom;
        $this->userTo = $userTo;
        $this->body = $body;
    }
}
