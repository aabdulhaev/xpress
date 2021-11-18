<?php

namespace common\forms\material;

use common\access\Rbac;
use common\models\Language;
use common\models\Material;
use common\models\Subject;
use common\models\Tag;
use common\models\Theme;
use common\models\User;
use common\validators\UuidValidator;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * @OA\Schema()
 */
class MaterialCreateForm extends Model
{
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
     * @OA\Property(enum={1, 2})
     * @var int
     */
    public $type;
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
     * @OA\Property(enum={1, 2})
     * @var int
     */
    public $source_type;
    /**
     * @OA\Property()
     * @var string
     */
    public $source_description;
    /**
     * @OA\Property()
     * @var int
     */
    public $learn_time;
    /**
     * @OA\Property(@OA\Items(type="string"))
     * @var array
     */
    public $tags = [];
    /**
     * @OA\Property(type="file")
     * @var string
     */
    public $image;

    public function rules(): array
    {
        return [
            ['title', 'trim'],
            ['title', 'required'],
            ['title', 'string', 'max' => 32],

            ['description', 'trim'],
            ['description', 'required'],
            ['description', 'string', 'max' => 2048],

            ['body', 'trim'],
            ['body', 'required'],

            ['type', 'integer'],
            ['type', 'required'],
            ['type', 'in', 'range' => [
                Material::TYPE_LIBRARY, Material::TYPE_TASK
            ]],
            [['type'], 'typeValidator'],

            ['theme', 'required'],
            ['theme', UuidValidator::class],
            ['theme', 'exist', 'targetClass' => Theme::class, 'targetAttribute' => 'theme_uuid'],

            ['subjects', 'required'],
            ['subjects', 'each', 'rule' => [
                UuidValidator::class
            ]],
            ['subjects', 'each', 'rule' => [
                'exist',
                'targetClass' => Subject::class,
                'targetAttribute' => 'subject_uuid'
            ]],

            ['language', 'required'],
            ['language', UuidValidator::class],
            ['language', 'exist', 'targetClass' => Language::class, 'targetAttribute' => 'language_uuid'],

            ['source_type', 'integer'],
            ['source_type', 'required'],
            ['source_type', 'in', 'range' => [
                Material::SOURCE_INTERNAL,
                Material::SOURCE_EXTERNAL
            ]],

            ['source_description', 'trim'],
            ['source_description', 'string', 'max' => 500],
            ['source_description', 'required', 'when' => function ($model) {
                return $model->source_type == Material::SOURCE_EXTERNAL;
            }],

            ['learn_time', 'integer'],
            ['learn_time', 'required'],

            ['tags', 'each', 'rule' => [
                UuidValidator::class
            ]],
            ['tags', 'each', 'rule' => [
                'exist',
                'targetClass' => Tag::class,
                'targetAttribute' => 'tag_uuid'
            ]],

            [['subjects', 'tags'], function ($attribute) {
                if (is_array($this->$attribute) && count($this->$attribute) > 3) {
                    $this->addError($attribute, 'Можно выбрать максимум 3 элемента.');
                }
            }],

            [
                'image', 'image', 'skipOnEmpty' => true,
                'minWidth' => 378, 'minHeight' => 222, 'extensions' => 'png, jpg, jpeg', 'mimeTypes' => 'image/*'
            ],
        ];
    }

    public function typeValidator($attribute)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        if ($user->role == Rbac::ROLE_MENTOR && $this->type == Material::TYPE_LIBRARY) {
            $this->addError($attribute, 'Ментору нельзя создавать материал библиотеки.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'type' => 'Тип материала',
            'title' => 'Название публикации',
            'description' => 'Описание для предпросмотра',
            'theme' => 'Тема',
            'language' => 'Язык',
            'subjects' => 'Цели развития',
            'source_type' => 'Тип источника',
            'source_description' => 'Описание источника',
            'tags' => 'Хештеги',
            'learn_time' => 'Время на ознакомления',
            'body' => 'Тело материала',
            'image' => 'Изображение',
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->image = UploadedFile::getInstanceByName('image');
            return true;
        }

        return false;
    }
}
