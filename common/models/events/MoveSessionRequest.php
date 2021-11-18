<?php

/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link http://good-master.com.ua/
 * Creator: VadimTs
 * Date: 26.04.2021
 */

namespace common\models\events;

use common\access\Rbac;
use common\forms\training\TrainingCreateForm;
use common\models\TrainingSession;

class MoveSessionRequest
{
    public $session;
    public $sender;
    public $recipient;
    public $comment;
    public $fromDateTime;
    public $toDateTime;

    /**
     * MoveSessionRequest constructor.
     * @param TrainingSession $session
     * @param TrainingCreateForm $form
     */
    public function __construct(TrainingSession $session, TrainingCreateForm $form)
    {
        $this->session = $session;
        $this->sender = $form->user;
        $this->recipient = $this->sender->role === Rbac::ROLE_EMP ? $session->coachOrMentor : $session->employee;
        $this->comment = $form->comment;
        $this->fromDateTime = $session->start_at_tc;
        $this->toDateTime = $form->start_at;
    }
}
