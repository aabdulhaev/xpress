<?php
namespace common\forms;

use common\access\Rbac;
use common\models\User;

/**
 *
 */
class EmployeeUpdateForm extends BaseUserUpdateForm
{
    public $mentor_program;
    public $coach_program;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($user, $config);
        $this->mentor_program = $this->user->inMentorProgram;
        $this->coach_program = $this->user->inCoachProgram;
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return array_merge(parent::rules(),
       [
           ['mentor_program', 'boolean'],
           ['coach_program', 'boolean'],

       ]);
    }

    public function scenarios()
    {
        return array_merge(
            parent::scenarios(),
            [
                 Rbac::ROLE_EMP => [
                'position','department','first_name','last_name','email'
            ]
        ]);
    }
}
