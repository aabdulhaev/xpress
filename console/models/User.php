<?php

namespace console\models;

use Yii;
use yii\base\{Exception, InvalidConfigException, NotSupportedException};
use yii\behaviors\TimestampBehavior;
use yii\db\{ActiveQuery, ActiveRecord};
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $avatar
 * @property string $avatar_lg
 * @property string $classifier
 * @property string $access_token
 * @property string $access_token_generated_at
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $patronymic
 * @property string $date_of_birth
 * @property string $gender
 * @property string $skype
 * @property string $website
 * @property string $position
 * @property int $level
 * @property int $score
 * @property int $modifier
 * @property int $modifier_invalid_level
 * @property int $classifier_change_count
 * @property int $classifier_count_reset_date
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 8;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    public const TOKEN_TIMESTAMP_DELIMITER = '@';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     * @return self
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['password_reset_token'], 'unique'],
            [['email'], 'email'],
            [
                ['password_hash', 'password_reset_token', 'email', 'first_name', 'last_name', 'patronymic'],
                'string',
                'max' => 255
            ],
            [['classifier', 'access_token', 'skype', 'website', 'position'], 'string', 'max' => 50],
            [['avatar', 'avatar_lg'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 12],
            [['gender'], 'string', 'max' => 1],
            [['phone'], 'match', 'pattern' => '/^\+\d{11}$/'],
            [['date_of_birth'], 'date', 'format' => "php:Y-m-d"],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['classifier'], 'in', 'range' => Yii::$app->params['classifiers']],
            [['password'], 'safe'],
            [['level', 'score', 'modifier_invalid_level', 'modifier'], 'integer'],
            [['classifier_change_count', 'classifier_count_reset_date'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException();
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public function getAuthKey()
    {
        throw new NotSupportedException();
    }

}
