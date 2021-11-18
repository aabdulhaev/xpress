<?php

namespace common\forms\meeting;

use common\validators\DateTimeValidator;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class MeetingSearchForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $start_at;
    /**
     * @OA\Property()
     * @var string
     */
    public $end_at;

    public function rules(): array
    {
        return [
            [['start_at', 'end_at'], DateTimeValidator::class, 'future' => true],
        ];
    }
}
