<?php

declare(strict_types=1);

namespace common\forms;

use common\models\User;
use yii\base\Model;

class ConnectRequestForm extends Model
{

    public $employee;
    public $mentor;
    public $comment;

    public $model;

    public function __construct(User $employee, User $mentor, $config = [])
    {
        $this->employee = $employee;
        $this->mentor = $mentor;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['comment','string','max' => 1024],
            ['comment', 'required'],
        ];
    }

    public function scenarios(): array
    {
        return array_merge(
            parent::scenarios(),
            [
                'employee' => ['comment'],
                'mentor' => ['comment'],
            ]
        );
    }
}
