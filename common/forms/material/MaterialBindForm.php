<?php

namespace common\forms\material;

use common\models\User;
use common\validators\UuidValidator;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class MaterialBindForm extends Model
{
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $users = [];

    public function rules(): array
    {
        return [
            ['users', 'required'],
            ['users', 'each', 'rule' => [
                UuidValidator::class
            ]],
            ['users', 'each', 'rule' => [
                'required'
            ]],
            ['users', 'each', 'rule' => [
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'user_uuid'
            ]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'users' => 'Пользователи'
        ];
    }
}
