<?php

namespace api\components;

use InvalidArgumentException;
use JsonSerializable;
use yii\base\Model;

class RestValidationError implements JsonSerializable
{
    protected $errors = [];

    public function __construct(Model $model)
    {
        if (empty($model->errors)) {
            throw new InvalidArgumentException();
        }
        foreach ($model->errors as $field => $errors) {
            foreach ($errors as $error) {
                $this->errors[] = ['field' => $field, 'message' => $error];
            }
        }
    }

    public function jsonSerialize()
    {
        return $this->errors;
    }
}
