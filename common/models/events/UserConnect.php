<?php


namespace common\models\events;


use common\models\User;

class UserConnect
{
    public $userFrom;
    public $employee;
    public $mentor;

    public function __construct(User $userFrom, User $employee, User $mentor)
    {
        $this->userFrom = $userFrom;
        $this->employee = $employee;
        $this->mentor = $mentor;
    }
}
