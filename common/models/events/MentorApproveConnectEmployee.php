<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 19.04.2021
 */


namespace common\models\events;


use common\models\EmployeeMentor;

class MentorApproveConnectEmployee
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