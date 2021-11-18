<?php

namespace api\modules\v1\models;

use common\models\Request;

class RequestSearch extends Request
{
    public function rules(): array
    {
        return [
            [['email', 'name'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_keys(Request::statuses())],
            [['type'], 'in', 'range' => array_keys(Request::types())],
        ];
    }
}
