<?php

declare(strict_types=1);

namespace common\forms\training;

use common\models\TrainingSession;
use common\models\User;
use common\validators\DateTimeValidator;
use yii\base\Model;

class TrainingEditForm extends Model
{
    public $status;
    public $start_at;
    public $duration;
    public $comment;
    public $model;
    public $editor;
    public $link;
    public $trainingSession;

    public function __construct(User $editor, TrainingSession $trainingSession = null, $config = [])
    {
        parent::__construct($config);
        $this->editor = $editor;
        if ($trainingSession) {
            $this->trainingSession = $trainingSession;
        }
    }

    public function rules(): array
    {
        return [
            ['comment','required','when' => function (self $model) {
                return $model->trainingSession && !$model->trainingSession->isFree();
            }],
            ['comment','string','max' => 1024],
            ['status', 'in', 'range' => array_keys(TrainingSession::statuses())],
            ['start_at', DateTimeValidator::class, 'future' => true],
            ['link', 'url', 'defaultScheme' => 'https'],
            ['duration','integer', 'min' => 60 * 5, 'max' => 360 * 600],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'comment' => 'Комментарий'
        ];
    }
}
