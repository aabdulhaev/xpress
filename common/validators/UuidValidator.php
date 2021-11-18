<?php

declare(strict_types=1);

namespace common\validators;

use Ramsey\Uuid\Uuid;
use Yii;
use yii\validators\Validator;

class UuidValidator extends Validator
{
    public $message = 'Значение «{attribute}» неверно.';

    public function validateAttribute($model, $attribute): void
    {
        if (!Uuid::isValid($model->$attribute)) {
            $message = parent::formatMessage($this->message, [
                'attribute' => $model->getAttributeLabel($attribute)
            ]);

            $this->addError($model, $attribute, $message);
        }
    }
}
