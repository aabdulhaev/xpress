<?php

namespace common\filters;

use yii\filters\Cors as DefCors;

class Cors extends DefCors
{
    public $cors = [

        'Origin' => ['*'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'Access-Control-Request-Headers' => ['*'],
        'Access-Control-Allow-Credentials' => null,
        'Access-Control-Max-Age' => 86400,
        'Access-Control-Expose-Headers' => [
            'X-Pagination-Current-Page',
            'X-Pagination-Page-Count',
            'X-Pagination-Per-Page',
            'X-Pagination-Total-Count',
            'X-Rate-Limit-Window',
            'Access-Control-Allow-Origin'
        ],
        'Access-Control-Allow-Origin' => ['*'],
    ];
}
