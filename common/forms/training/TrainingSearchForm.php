<?php

namespace common\forms\training;

use common\models\TrainingSession;
use common\validators\DateTimeValidator;
use yii\base\Model;

class TrainingSearchForm extends Model
{
    public $start_at_from;
    public $start_at_to;
    public $status;
    public $scenario;

    public function rules(): array
    {
        return [
            [['start_at_from', 'start_at_to'], DateTimeValidator::class, 'future' => true],
            ['status', 'statusValidation'],
            [
                'scenario', 'in',
                'range' => [
                    'confirmed',
                    'for-rating',
                    'for-complete',
                    'free',
                    'wait-confirm',
                    'need-confirm'
                ]
            ]
        ];
    }

    public function statusValidation($attribute)
    {
        if (is_array($this->$attribute)) {
            foreach ($this->$attribute as $status) {
                if (!array_key_exists($status, TrainingSession::statuses())) {
                    $this->addError($attribute, 'Неверное значение статуса');
                }
            }
        } else {
            if (!array_key_exists($this->$attribute, TrainingSession::statuses())) {
                $this->addError($attribute, 'Неверное значение статуса');
            }
        }
    }
}
