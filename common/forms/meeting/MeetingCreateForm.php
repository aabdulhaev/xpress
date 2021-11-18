<?php

declare(strict_types=1);

namespace common\forms\meeting;

use common\models\Meeting;
use common\models\TrainingSession;
use common\validators\DateTimeValidator;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class MeetingCreateForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $training_uuid;
    /**
     * @OA\Property()
     * @var string
     */
    public $start_at;

    public function rules(): array
    {
        return [
            [
                'training_uuid',
                'exist',
                'targetClass' => TrainingSession::class,
                'targetAttribute' => 'training_uuid'
            ],
            [
                'training_uuid',
                'unique',
                'targetClass' => Meeting::class,
                'targetAttribute' => 'training_uuid'
            ],
            ['start_at', DateTimeValidator::class, 'future' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'training_uuid' => 'Тренинг',
            'start_at' => 'Дата начала',
        ];
    }
}
