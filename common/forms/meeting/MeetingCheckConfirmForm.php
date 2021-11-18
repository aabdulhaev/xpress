<?php

declare(strict_types=1);

namespace common\forms\meeting;

use common\models\UserMeeting;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class MeetingCheckConfirmForm extends Model
{
    /**
     * @OA\Property()
     * @OA\Required()
     * @var string
     */
    public $token;

    public function rules(): array
    {
        return [
            [['token'], 'required'],
            [['token'], 'string', 'max' => 32],

            [
                'token',
                'exist',
                'targetClass' => UserMeeting::class,
                'targetAttribute' => 'token'
            ],
        ];
    }
}
