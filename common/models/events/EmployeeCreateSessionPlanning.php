<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 20.04.2021
 */


namespace common\models\events;


use common\models\TrainingSession;

class EmployeeCreateSessionPlanning
{
    public $employee;
    public $mentor;
    public $dateStart;

    public function __construct(TrainingSession $model)
    {
        $this->employee = $model->employee;
        $this->mentor = $model->coachOrMentor;
        $this->dateStart = \Yii::$app->formatter->asDatetime($model->start_at_tc,'php:d.m.Y H:i');
    }
}
