<?php

namespace common\forms;

use yii\base\Model;

/**
 * @OA\Schema()
 */
class DirectorySearchForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $title;
    /**
     * @OA\Property()
     * @var string
     */
    public $description;
    /**
     * @OA\Property(enum={0,1})
     * @var int
     */
    public $status;

    public function rules(): array
    {
        return [
            ['status', 'integer'],
            ['status', 'in', 'range' => [0, 1]],

            ['title', 'string', 'max' => 32],
            ['description', 'string', 'max' => 2048],
        ];
    }
}
