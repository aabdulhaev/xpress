<?php

declare(strict_types=1);

namespace common\validators;

use yii\validators\Validator;

class DateTimeValidator extends Validator
{

    public $future = false;
    public $format = 'Y-m-d H:i:sP';

    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;

        if (!$this->isCorrect($value)) {
            $this->addError($model, $attribute, 'Не корректный формат даты. Укажите дату в формате Y-m-d H:i:sZ');
        }

        if (($this->future === false) && !$this->isFuture($value)) {
            $this->addError($model, $attribute, 'Укажите дату в будущем.');
        }

        $model->$attribute = $this->addTimezone($value);
    }

    public function isCorrect($date): bool
    {
        $d = \DateTimeImmutable::createFromFormat($this->format, $date);
        return $d && $d->format($this->format) === $date;
    }

    public function isFuture($date): bool
    {
        $d = \DateTimeImmutable::createFromFormat($this->format, $date);
        $now = new \DateTimeImmutable();
        return $d >= $now;
    }

    public function addTimezone($date): string
    {
        try {
            $d = \DateTimeImmutable::createFromFormat($this->format, $date, new \DateTimeZone('UTC'));

            if ($d == false) {
                return '';
            }

            return $d->format('Y-m-d H:i:sP');
        } catch (\Exception $e) {
            \Yii::error($e, __METHOD__);
        }

        return '';
    }
}
