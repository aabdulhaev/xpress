<?php

namespace api\modules\v1\models;

use common\access\Rbac;
use common\models\User;

/**
 * @OA\Schema()
 */
class UserSearch extends User
{
    /**
     * @OA\Property()
     * @var string
     */
    public $email;

    /**
     * @OA\Property()
     * @var string
     */
    public $first_name;

    /**
     * @OA\Property()
     * @var string
     */
    public $last_name;

    /**
     * @OA\Property()
     * @var string
     */
    public $department;

    /**
     * @OA\Property()
     * @var string
     */
    public $position;

    /**
     * @OA\Property()
     * @var int
     */
    public $status;

    /**
     * @OA\Property()
     * @var string
     */
    public $phone;

    /**
     * @OA\Property()
     * @var int
     */
    public $role;

    public function rules(): array
    {
        return [
            [['email', 'first_name', 'last_name', 'department', 'position'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 12],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_SUSPENDED]],
            [['role'], 'in', 'range' => array_keys(Rbac::roles())]
        ];
    }
}
