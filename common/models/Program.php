<?php

namespace common\models;

use common\access\Rbac;
use common\models\queries\ProgramQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "program".
 *
 * @property string $program_uuid
 * @property string $name
 * @property string $description
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $blocked_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $blocked_by
 * @property int $status
 */
class Program extends ActiveRecord
{
    public const MENTOR_UUID = '1eb1f6e0-59d9-6890-8de1-72223d211ced';
    public const COACH_UUID = '1eb1f6e0-59de-66b0-0918-bee55fb8d282';

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;


    public static function UuidByRole(){
        return [
            Rbac::ROLE_MENTOR => self::MENTOR_UUID,
            Rbac::ROLE_COACH => self::COACH_UUID,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program';
    }

    public function behaviors(): array
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     * @return ProgramQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProgramQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_uuid', 'name', 'description', 'created_at', 'created_by'], 'required'],
            [['program_uuid', 'created_by', 'updated_by', 'blocked_by'], 'string'],
            [['created_at', 'updated_at', 'blocked_at'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'blocked_at'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 2048],
            [['program_uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'program_uuid' => 'Program Uuid',
            'name' => 'Name',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'blocked_at' => 'Blocked At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'blocked_by' => 'Blocked By',
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
