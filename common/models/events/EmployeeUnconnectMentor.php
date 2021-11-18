<?php

namespace common\models\events;

use common\models\EmployeeMentor;

class EmployeeUnconnectMentor
{
    public $employee;
    public $mentor;
    public $comment;

    public function __construct(EmployeeMentor $assigment)
    {
        $this->employee = $assigment->employee;
        $this->mentor = $assigment->mentor;
        $this->comment = $assigment->comment;
    }
}
