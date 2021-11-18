<?php

namespace common\repositories;

use common\access\Rbac;
use common\forms\material\MaterialBindForm;
use common\forms\material\MaterialCreateForm;
use common\forms\material\MaterialSearchForm;
use common\forms\material\MaterialUnbindForm;
use common\forms\material\MaterialUpdateForm;
use common\models\Material;
use common\models\MaterialUser;
use common\models\User;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MaterialRepository
{
    public function get($id): Material
    {
        return $this->getBy(['material_uuid' => $id]);
    }

    public function save(Material $material): void
    {
        if (!$material->save()) {
            throw new \RuntimeException('Ошибка сохранения материала');
        }
    }

    public function remove(Material $material): void
    {
        if (!$material->delete()) {
            throw new \RuntimeException('Ошибка удаления материала');
        }
    }

    public function getByUuid(array $uuids): ActiveQuery
    {
        return Material::find()->andWhere(['IN', 'material_uuid', $uuids]);
    }

    private function getBy(array $condition): Material
    {
        /** @var Material $material */
        $material = Material::find()->andWhere($condition)->limit(1)->one();
        if (!$material) {
            throw new NotFoundHttpException('Материал не найден');
        }

        return $material;
    }

    public function search(MaterialSearchForm $form): ActiveDataProvider
    {
        /** @var $user User */
        $user = Yii::$app->user->identity;
        $inCoachProgram = $user->inCoachProgram ? 1 : 0;

        $query = Material::find()
            ->alias('m')
            ->distinct()
            ->joinWith([
                'tags t',
                'subjects s',
                'language l',
                'theme th',
                'userAssignments ua' => function ($query) use ($user) {
                    $query->andOnCondition(['=', 'ua.user_uuid', $user->user_uuid]);
                }
            ])
            ->andWhere(['!=', 'm.status', Material::STATUS_DELETED])
            ->andWhere(['or',
                ['=', 'm.created_by', $user->user_uuid],
                [
                    'or',
                    [
                        'and',
                        ['=', 'm.type', Material::TYPE_LIBRARY],
                        ['=', 'm.status', Material::STATUS_PUBLISHED],
                        [
                            'or',
                            "'" . $user->role . "'='" . Rbac::ROLE_COACH . "'",
                            [
                                'and',
                                "'" . $user->role . "'='" . Rbac::ROLE_EMP . "'",
                                ['=', $inCoachProgram, 1],
                            ]
                        ]
                    ],
                    [
                        'and',
                        ['=', 'm.type', Material::TYPE_TASK],
                        ['=', 'ua.accessed', MaterialUser::ACCESSED],
                    ]
                ]
            ]);

        if (!empty($form->scenario)) {
            switch ($form->scenario) {
                case 'elected':
                    $query = $query->andWhere(['=', 'ua.elected', MaterialUser::ELECTED]);
                    break;
                case 'learned':
                    $query = $query->andWhere(['=', 'ua.learned', MaterialUser::LEARNED]);
                    break;
                case 'coach':
                    $coaches = ArrayHelper::getColumn($user->approvedCoaches, 'user_uuid');
                    $query = $query->andWhere([
                        'or',
                        [
                            'and',
                            ['=', 'm.type', Material::TYPE_LIBRARY],
                            ['IN', 'm.created_by', $coaches],
                        ],
                        [
                            'and',
                            ['=', 'm.type', Material::TYPE_TASK],
                            ['IN', 'ua.created_by', $coaches],
                        ],
                    ]);
                    break;
                case 'author':
                    $query = $query->andWhere(['=', 'm.created_by', $user->user_uuid]);
                    break;
            }
        }

        if (!empty($form->search_str)) {
            $query = $query->andWhere([
                'or',
                ['ILIKE', 'm.title', $form->search_str],
                ['ILIKE', 'm.description', $form->search_str],
                ['ILIKE', 'm.body', $form->search_str],
                ['ILIKE', 'th.title', $form->search_str],
                ['ILIKE', 'l.title', $form->search_str],
                ['ILIKE', 's.title', $form->search_str],
                ['ILIKE', 't.title', $form->search_str]
            ]);
        }

        if (!empty($form->status)) {
            $query = $query->andWhere(['=', 'm.status', $form->status]);
        }

        if (!empty($form->type)) {
            $query = $query->andWhere(['=', 'm.type', $form->type]);
        }

        return $this->getProvider($query);
    }

    private function getProvider(QueryInterface $query): ActiveDataProvider
    {
        $requestParams = Yii::$app->getRequest()->getQueryParams();

        return new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'params' => $requestParams,
                ],
                'sort' => [
                    'params' => $requestParams,
                ],
            ]
        );
    }

    public function changeElected(MaterialUser $materialUser): void
    {
        $materialUser->elected = ($materialUser->isElected ? MaterialUser::NOT_ELECTED : MaterialUser::ELECTED);
        $materialUser->save(false);
    }

    public function changeLearned(MaterialUser $materialUser): void
    {
        $materialUser->learned = ($materialUser->isLearned ? MaterialUser::NOT_LEARNED : MaterialUser::LEARNED);
        $materialUser->save(false);
    }

    public function bind(Material $material, MaterialBindForm $model): void
    {
        foreach ($model->users as $user_uuid) {
            $material->assignUser($user_uuid);
        }

        $this->save($material);
    }

    public function unbind(Material $material, MaterialUnbindForm $model): void
    {
        foreach ($model->users as $user_uuid) {
            $material->revokeUser($user_uuid);
        }

        $this->save($material);
    }

    public function create(MaterialCreateForm $form): Material
    {
        $model = new Material();
        $model->setAttributes($form->toArray(), false);
        $model->material_uuid = Uuid::uuid6();
        $model->theme_uuid = $form->theme;
        $model->language_uuid = $form->language;
        $model->img_name = $form->image;
        if ($model->type == Material::TYPE_TASK) {
            $model->status = Material::STATUS_PUBLISHED;
        }

        foreach (($form->subjects ? : []) as $subject) {
            if (!$subject) continue;
            $model->assignSubject($subject);
        }

        foreach (($form->tags ? : []) as $tag) {
            if (!$tag) continue;
            $model->assignTag($tag);
        }

        $this->save($model);

        return $model;
    }

    public function update(Material $model, MaterialUpdateForm $form): void
    {
        $model->setAttributes($form->toArray(), false);
        $model->theme_uuid = $form->theme;
        $model->language_uuid = $form->language;

        $model->revokeSubjects();
        $model->revokeTags();
        $this->save($model);

        foreach (($form->subjects ? : []) as $subject) {
            if (!$subject) continue;
            $model->assignSubject($subject);
        }

        foreach (($form->tags ? : []) as $tag) {
            if (!$tag) continue;
            $model->assignTag($tag);
        }

        if ($form->image) {
            $model->editImage($form->image);
        } elseif ($form->image_remove) {
            $model->removeImage();
        }

        $this->save($model);
    }
}
