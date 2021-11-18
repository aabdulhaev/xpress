<?php

namespace api\modules\v1\models;

use common\models\Client;

/**
 * @OA\Schema()
 */
class ClientSearch extends Client
{
    /**
     * @OA\Property()
     * @var string
     */
    public $name;

    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 255]
        ];
    }
}
