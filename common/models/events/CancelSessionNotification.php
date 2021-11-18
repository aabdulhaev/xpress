<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link http://good-master.com.ua/
 * Creator: VadimTs
 * Date: 26.04.2021
 */

namespace common\models\events;


use common\access\Rbac;
use common\forms\training\TrainingEditForm;
use common\models\TrainingSession;

class CancelSessionNotification
{
    public $session;
    public $sender;
    public $recipient;
    public $comment;

    /**
     * MoveSessionRequest constructor.
     * @param TrainingSession $session
     * @param TrainingEditForm $form
     */
    public function __construct(TrainingSession $session,TrainingEditForm $form)
    {
        $this->session = $session;
        $this->sender = $form->editor;
        $this->recipient = $this->sender->role === Rbac::ROLE_EMP ? $session->coachOrMentor : $session->employee;
        $this->comment = $form->comment;
    }
}
