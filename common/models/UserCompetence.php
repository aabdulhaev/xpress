<?php

namespace common\models;

use common\models\queries\UserCompetenceQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "user_competence".
 *
 * @property string $user_uuid
 * @property string $competence_uuid
 * @property int $created_at
 * @property int|null $updated_at
 * @property int $status
 *
 * @property User $user
 */
class UserCompetence extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_competence}}';
    }

    /**
     * {@inheritdoc}
     * @return UserCompetenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserCompetenceQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uuid' => 'User Uuid',
            'competence_uuid' => 'Competence Uuid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['user_uuid' => 'user_uuid']);
    }

    public static function create($competence_uuid): self
    {
        $assignment = new static();
        $assignment->competence_uuid = $competence_uuid;
        return $assignment;
    }

    public function delete()
    {
        return $this->softDelete();
    }
}
