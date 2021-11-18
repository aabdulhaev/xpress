<?php

namespace common\forms\material;

use common\models\Language;
use common\models\Material;
use common\models\Subject;
use common\models\Tag;
use common\models\Theme;
use common\models\User;
use common\validators\UuidValidator;
use yii\base\Model;

/**
 * @OA\Schema()
 */
class MaterialSearchForm extends Model
{
    /**
     * @OA\Property()
     * @var string
     */
    public $scenario;
    /**
     * @OA\Property(enum={"elected", "learned", "coach", "author"})
     * @var string
     */
    public $search_str;
    /**
     * @OA\Property()
     * @var string
     */
    public $title;
    /**
     * @OA\Property()
     * @var string
     */
    public $description;
    /**
     * @OA\Property()
     * @var string
     */
    public $body;
    /**
     * @OA\Property()
     * @var string
     */
    public $theme;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $subjects = [];
    /**
     * @OA\Property()
     * @var string
     */
    public $language;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $tags = [];
    /**
     * @OA\Property(enum={1, 2, 3})
     * @var int
     */
    public $status;
    /**
     * @OA\Property(enum={1, 2})
     * @var int
     */
    public $type;
    /**
     * @OA\Property()
     * @var string
     */
    public $created_by;
    /**
     * @OA\Property()
     * @var string
     */
    public $updated_by;

    public function rules(): array
    {
        return [
            [['search_str'], 'string', 'max' => 255],
            [
                'scenario',
                'in',
                'range' => ['elected', 'learned', 'coach', 'author']
            ],

            ['theme', 'string'],
            ['theme', UuidValidator::class],
            ['theme', 'exist', 'targetClass' => Theme::class, 'targetAttribute' => 'theme_uuid'],

            ['language', 'string'],
            ['language', UuidValidator::class],
            ['language', 'exist', 'targetClass' => Language::class, 'targetAttribute' => 'language_uuid'],

            ['subjects', 'each', 'rule' => [
                UuidValidator::class
            ]],
            ['subjects', 'each', 'rule' => [
                'exist',
                'targetClass' => Subject::class,
                'targetAttribute' => 'subject_uuid'
            ]],

            ['tags', 'each', 'rule' => [
                UuidValidator::class
            ]],
            ['tags', 'each', 'rule' => [
                'exist',
                'targetClass' => Tag::class,
                'targetAttribute' => 'tag_uuid'
            ]],
            ['status', 'integer'],
            ['status', 'in', 'range' => [
                Material::STATUS_NOT_PUBLISHED, Material::STATUS_PUBLISHED, Material::STATUS_DECLINED
            ]],

            ['type', 'integer'],
            ['type', 'in', 'range' => [
                Material::TYPE_LIBRARY, Material::TYPE_TASK
            ]],
            [['created_by', 'updated_by'], UuidValidator::class],
            [['created_by', 'updated_by'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'user_uuid'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'scenario' => 'Раздел',
            'search_str' => 'Строка поиска',
            'title' => 'Название публикации',
            'description' => 'Описание для предпросмотра',
            'body' => 'Тело материала',
            'theme' => 'Тема',
            'subjects' => 'Цели развития',
            'language' => 'Язык',
            'tags' => 'Хештеги',
            'status' => 'Статус',
            'type' => 'Тип материала',
            'created_by' => 'Добавил',
            'updated_by' => 'Редактировал',
        ];
    }
}
