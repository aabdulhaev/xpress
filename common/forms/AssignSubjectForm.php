<?php

declare(strict_types=1);

namespace common\forms;

use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @OA\Schema()
 */
class AssignSubjectForm extends Model
{
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $subjects;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->subjects = ArrayHelper::getColumn($user->subjectAssignments, 'subject_uuid');
    }

    public function rules(): array
    {
        return [
            ['subjects', 'each', 'rule' => ['string']],
            ['subjects', 'default', 'value' => []],
        ];
    }
}
