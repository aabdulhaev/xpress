<?php

namespace common\forms;

use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property ActiveRecord $modelClass
 * @OA\Schema(required={"title"})
 */
class DirectoryForm extends Model
{
    public $modelClass;
    /**
     * @OA\Property()
     * @OA\Required()
     * @var string
     */
    public $title;
    /**
     * @OA\Property()
     * @var string
     */
    public $description;

    public function rules(): array
    {
        return [
            ['title', 'trim'],
            ['title', 'required'],
            ['title', 'string', 'max' => 32],
            ['title', 'unique',
                'targetClass' => $this->modelClass,
                'targetAttribute' => 'title',
                'filter' => function (ActiveQuery $query) {
                    if ($this->modelClass->isNewRecord) {
                        return $query;
                    }
                    $primaryKey = $this->modelClass::primaryKey()[0];
                    return $query->andWhere(['!=', $primaryKey, $this->modelClass->$primaryKey]);
                }
            ],

            ['description', 'trim'],
            ['description', 'string', 'max' => 2048],
        ];
    }
}
