<?php

declare(strict_types=1);

namespace common\forms\training;

use common\models\User;
use yii\base\Model;

class TrainingRejectMoveRequestForm extends Model
{
    public $comment;
    public $user;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
    }


    public function rules(): array
    {
        return [
            ['comment','string'],
            ['comment','required']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'comment' => 'Комментарий'
        ];
    }
}
