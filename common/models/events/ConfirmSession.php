<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 22.04.2021
 */


namespace common\models\events;


use common\access\Rbac;
use common\models\TrainingSession;
use common\models\User;

class ConfirmSession
{
    public $session;
    public $sender;
    public $recipient;

    public function __construct(TrainingSession $session,User $sender)
    {
        $this->session = $session;
        $this->sender = $sender;
        $this->recipient = $sender->role === Rbac::ROLE_EMP ? $session->coachOrMentor : $session->employee;
    }
}