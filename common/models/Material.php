<?php

namespace common\models;

use common\behaviors\uploadBehavior\ImageUploadBehavior;
use common\models\queries\MaterialSubjectQuery;
use common\models\queries\MaterialTagQuery;
use common\models\queries\MaterialUserQuery;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 *
 * @OA\Schema()
 *
 * @OA\Property (property="material_uuid", type="string")
 * @OA\Property (property="type", type="integer", enum={1, 2})
 * @OA\Property (property="title", type="string")
 * @OA\Property (property="description", type="string")
 * @OA\Property (property="body", type="string")
 * @OA\Property (property="image", type="string")
 * @OA\Property (property="source_type", type="integer", enum={1, 2})
 * @OA\Property (property="source_description", type="string")
 * @OA\Property (property="learn_time", type="integer")
 * @OA\Property (property="status", type="integer", enum={1, 2})
 *
 * This is the model class for table "materials".
 *
 * @property string $material_uuid [uuid]
 * @property int $type [smallint]
 * @property string $title [varchar(32)]
 * @property string $description [varchar(2048)]
 * @property string $body
 * @property string $img_name [varchar(1048)]
 * @property string $theme_uuid [uuid]
 * @property string $language_uuid [uuid]
 * @property int $source_type [smallint]
 * @property string $source_description [varchar(500)]
 * @property string $learn_time [integer]
 * @property int $status [smallint]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 * @property string $approve_at [integer]
 * @property string $created_by [uuid]
 * @property string $updated_by [uuid]
 * @property string $approve_by [uuid]
 *
 * @property User $author
 * @property Language $language
 * @property Theme $theme
 * @property Tag[] $tags
 * @property MaterialTag[] $tagAssignments
 * @property Subject[] $subjects
 * @property MaterialSubject[] $subjectAssignments
 * @property User[] $users
 * @property MaterialUSer[] $userAssignments
 *
 * @mixin ImageUploadBehavior
 */
class Material extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_NOT_PUBLISHED = 1;
    public const STATUS_PUBLISHED = 2;
    public const STATUS_DECLINED = 3;

    public const TYPE_LIBRARY = 1;
    public const TYPE_TASK = 2;

    public const SOURCE_INTERNAL = 1;
    public const SOURCE_EXTERNAL = 2;

    public static function statuses(): array
    {
        return [
            self::STATUS_DELETED => 'Удален',
            self::STATUS_NOT_PUBLISHED => 'Не опубликован',
            self::STATUS_PUBLISHED => 'Опубликован',
            self::STATUS_DECLINED => 'Отклонен',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_LIBRARY => 'Для библиотеки',
            self::TYPE_TASK => 'Задание',
        ];
    }

    public static function tableName(): string
    {
        return '{{%materials}}';
    }

    public function behaviors(): array
    {
        return [
            'user' => [
                'class' => BlameableBehavior::class
            ],
            'time' => [
                'class' => TimestampBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ],
            'img' => [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'img_name',
                'createThumbsOnRequest' => true,
                'filePath' => '@storageRoot/origin/materials/[[pk]]/[[pk]].[[extension]]',
                'fileUrl' => '@storage/origin/materials/[[pk]]/[[pk]].[[extension]]',
                'thumbPath' => '@storageRoot/cache/materials/[[pk]]/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '@storage/cache/materials/[[pk]]/[[profile]]_[[pk]].[[extension]]',
                'thumbs' => [
                    'thumb' => ['width' => 320, 'height' => 320],
                ],
            ],
            [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'tagAssignments',
                    'subjectAssignments',
                    'userAssignments',
                ],
            ],
        ];
    }

    public function fields(): array
    {
        return [
            'material_uuid',
            'type',
            'title',
            'description',
            'body',
            'image' => static function (self $material) {
                return $material->getBehavior('img')->getThumbFileUrl('img_name');
            },
            'source_type',
            'source_description',
            'learn_time',
            'status',
            'created_at',
            'created_at_format' => static function (self $material) {
                return Yii::$app->formatter->asDatetime($material->created_at, 'php:Y-m-d H:i:sP');
            },
            'created_by',
            'updated_by',
        ];
    }

    public function extraFields(): array
    {
        return [
            'author',
            'theme',
            'subjects',
            'language',
            'tags',
            'others' => function (self $material) {
                $isElected = false;
                $isLearned = false;

                /** @var MaterialUser $materialUser */
                /** @var User $user */

                $user = Yii::$app->user->identity;
                if ($materialUser = $material->getMaterialUser($user)) {
                    $isElected = $materialUser->isElected;
                    $isLearned = $materialUser->isLearned;
                }

                return [
                    'is_elected' => $isElected,
                    'is_learned' => $isLearned,
                ];
            },
            'userAssignments',
            'users'
        ];
    }

    public function delete()
    {
        return $this->softDelete();
    }

    public function assignSubject($subject_uuid): void
    {
        $assignments = $this->subjectAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->subject_uuid == $subject_uuid) {
                $new = false;

                if (!$assignment->isActive()) {
                    $assignment->status = MaterialSubject::STATUS_ACTIVE;
                    $assignment->save();
                }
            }
        }

        if ($new) {
            $assignments[] = MaterialSubject::create($this->material_uuid, $subject_uuid);
        }

        $this->subjectAssignments = $assignments;
    }

    public function revokeSubject($tag_uuid): void
    {
        $assignments = $this->subjectAssignments;

        foreach ($assignments as &$assignment) {
            if ($assignment->subject_uuid == $tag_uuid) {
                if ($assignment->isActive()) {
                    $assignment->status = MaterialSubject::STATUS_DELETED;
                    $assignment->save();
                }

                $this->subjectAssignments = $assignments;
                return;
            }
        }

        throw new \DomainException('Тег не найден.');
    }

    public function revokeSubjects(): void
    {
        $this->subjectAssignments = [];
    }

    public function getSubjectAssignments(): ActiveQuery
    {
        return $this->hasMany(MaterialSubject::class, ['material_uuid' => 'material_uuid']);
    }

    public function getSubjects(): ActiveQuery
    {
        return $this->hasMany(Subject::class, ['subject_uuid' => 'subject_uuid'])
            ->via('subjectAssignments', function (MaterialSubjectQuery $relation) {
                return $relation->andOnCondition([MaterialSubject::tableName() . '.status' => MaterialSubject::STATUS_ACTIVE]);
            });
    }

    public function assignTag($tag_uuid): void
    {
        $assignments = $this->tagAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->tag_uuid == $tag_uuid) {
                $new = false;

                if (!$assignment->isActive()) {
                    $assignment->status = MaterialTag::STATUS_ACTIVE;
                    $assignment->save();
                }
            }
        }

        if ($new) {
            $assignments[] = MaterialTag::create($this->material_uuid, $tag_uuid);
        }

        $this->tagAssignments = $assignments;
    }

    public function revokeTag($tag_uuid): void
    {
        $assignments = $this->tagAssignments;

        foreach ($assignments as &$assignment) {
            if ($assignment->tag_uuid == $tag_uuid) {
                if ($assignment->isActive()) {
                    $assignment->status = MaterialTag::STATUS_DELETED;
                    $assignment->save();
                }

                $this->tagAssignments = $assignments;
                return;
            }
        }

        throw new \DomainException('Тег не найден.');
    }

    public function revokeTags(): void
    {
        $this->tagAssignments = [];
    }

    public function getTagAssignments(): ActiveQuery
    {
        return $this->hasMany(MaterialTag::class, ['material_uuid' => 'material_uuid']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['tag_uuid' => 'tag_uuid'])
            ->via('tagAssignments', function (MaterialTagQuery $relation) {
                return $relation->andOnCondition([MaterialTag::tableName() . '.status' => MaterialTag::STATUS_ACTIVE]);
            });
    }

    public function assignUser($user_uuid): void
    {
        $assignments = $this->userAssignments;
        $new = true;
        foreach ($assignments as $assignment) {
            if ($assignment->user_uuid == $user_uuid) {
                $new = false;

                if (!$assignment->isAccessed) {
                    $assignment->accessed = MaterialUser::ACCESSED;
                    $assignment->save();
                }
            }
        }

        if ($new) {
            $assignments[] = MaterialUser::create($this->material_uuid, $user_uuid, MaterialUser::ACCESSED);
        }

        $this->userAssignments = $assignments;
    }

    public function revokeUser($user_uuid): void
    {
        $assignments = $this->userAssignments;

        foreach ($assignments as &$assignment) {
            if ($assignment->user_uuid == $user_uuid) {
                if ($assignment->isAccessed) {
                    $assignment->accessed = MaterialUser::NOT_ACCESSED;
                    $assignment->save();
                }

                $this->userAssignments = $assignments;
                return;
            }
        }

        throw new \DomainException('Связь не найдена.');
    }

    public function revokeUsers(): void
    {
        $this->userAssignments = [];
    }

    public function getUserAssignments(): ActiveQuery
    {
        return $this->hasMany(MaterialUser::class, ['material_uuid' => 'material_uuid']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['user_uuid' => 'user_uuid'])
            ->via('userAssignments', function (MaterialUserQuery $relation) {
                return $relation->andOnCondition([MaterialUser::tableName() . '.accessed' => MaterialUser::ACCESSED]);
            });
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_uuid' => 'created_by']);
    }

    public function getTheme(): ActiveQuery
    {
        return $this->hasOne(Theme::class, ['theme_uuid' => 'theme_uuid']);
    }

    public function getLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_uuid' => 'language_uuid']);
    }

    public function getMaterialUser(User $user): ?ActiveRecord
    {
        if ($materialUsers = $this->getUserAssignments()) {
            return $materialUsers->andWhere(['user_uuid' => $user->user_uuid])->one();
        }

        return null;
    }

    public function editImage($file): void
    {
        $this->removeImage();
        $this->img_name = $file;
    }

    public function removeImage(): void
    {
        $this->cleanFiles();
        $oldAttributes = $this->oldAttributes;
        unset($oldAttributes['img_name']);
        $this->oldAttributes = $oldAttributes;
        $this->img_name = null;
    }
}
