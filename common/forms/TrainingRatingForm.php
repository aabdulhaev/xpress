<?php

declare(strict_types=1);

namespace common\forms;

use common\models\TrainingSession;
use common\models\User;
use yii\base\Model;

class TrainingRatingForm extends Model{


    public $rate;
    public $comment;
    public $subjects;

    public $author;
    public $training;

    public function __construct(User $author, TrainingSession $training, $config = [])
    {
        parent::__construct($config);
        $this->author = $author;
        $this->training = $training;
    }

    public function rules() : array
    {
        return [
            ['rate','integer','max' => 10, 'min' => 1],
            ['comment', 'string', 'min' => 2, 'max' => 2000],
            ['subjects', 'filter', 'filter' => function($val){
                if (is_array($val)) {
                    return $val;
                }
                return false;
            }],
        ];
    }
}
