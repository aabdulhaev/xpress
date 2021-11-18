<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 05.05.2021
 */


namespace common\models\events;


use common\access\Rbac;
use common\forms\TrainingRatingForm;
use common\models\Subject;
use common\models\TrainingSession;
use common\models\User;

class UserSessionRating
{
    public $session;
    public $sender;
    public $recipient;
    public $comment;
    public $started_at;
    public $subjects;
    public $rate;

    public function __construct(TrainingSession $session,TrainingRatingForm $form)
    {
        $this->session = $session;
        $this->sender = $form->author;
        $this->recipient = $this->sender->role === Rbac::ROLE_EMP ? $session->coachOrMentor : $session->employee;
        $this->comment = $form->comment;
        $this->rate = $form->rate;
        /** format: 2021-03-23 14:00:00+00 */
        $this->started_at = $session->start_at_tc;
        $this->subjects = Subject::findAll(['subject_uuid'=>$form->subjects]);
    }
}
