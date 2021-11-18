<?php

namespace common\models;

use common\models\queries\RequestQuery;
use Ramsey\Uuid\Uuid;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "request".
 *
 * @property string $request_uuid
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int $type
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string|null $updated_by
 * @property string|null $blocked_by
 */
class Request extends ActiveRecord
{
    public const STATUS_NEW = 0;
    public const STATUS_DELETED = 1;
    public const STATUS_DECLINE = 10;
    public const STATUS_APPROVED = 20;

    public const TYPE_CLIENT = 10;
    public const TYPE_COACH = 20;

    public static function statuses() :array
    {
        return [
          self::STATUS_NEW => 'Новая',
          self::STATUS_DECLINE => 'Отклонение',
          self::STATUS_APPROVED => 'Одобрение'
        ];
    }

    public static function types() :array
    {
        return [
            self::TYPE_CLIENT => 'Клиент',
            self::TYPE_COACH => 'Тренер'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     * @return RequestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RequestQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    /*public function rules()
    {
        return [
            [['request_uuid', 'name', 'email', 'phone', 'type', 'description', 'status', 'created_at'], 'required'],
            [['request_uuid', 'updated_by', 'blocked_by'], 'string'],
            [['type', 'status', 'created_at', 'updated_at', 'blocked_at'], 'default', 'value' => null],
            [['type', 'status', 'created_at', 'updated_at', 'blocked_at'], 'integer'],
            [['name', 'email'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 12],
            [['description'], 'string', 'max' => 2048],
            [['request_uuid'], 'unique'],
        ];
    }*/


    public function behaviors(): array
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => null
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    public function transactions() : array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'request_uuid' => 'Request Uuid',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'type' => 'Type',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public static function create($name, $email, $phone, $description, $type) :self
    {
        $model = new static();
        $model->name = $name;
        $model->email = $email;
        $model->phone = $phone;
        $model->description = $description;
        $model->type = $type;
        $model->status = self::STATUS_NEW;
        $model->request_uuid = Uuid::uuid6();
        return $model;
    }

    public function edit($status) :void
    {
        $this->status = $status;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
