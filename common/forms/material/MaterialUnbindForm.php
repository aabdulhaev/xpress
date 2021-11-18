<?php

namespace common\forms\material;

use common\models\Material;
use common\models\MaterialUser;
use common\validators\UuidValidator;
use yii\base\Model;
use yii\db\ActiveQuery;

/**
 * @OA\Schema()
 */
class MaterialUnbindForm extends Model
{
    /** @var Material */
    private $material;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $users = [];

    public function __construct(Material $material, $config = [])
    {
        $this->material = $material;
        parent::__construct($config);
    }

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
                'targetClass' => MaterialUser::class,
                'targetAttribute' => 'user_uuid',
                'filter' => function (ActiveQuery $query) {
                    return $query->andWhere(['=', 'material_uuid', $this->material->material_uuid])
                        ->andWhere(['=', 'accessed', MaterialUser::ACCESSED]);
                }
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
