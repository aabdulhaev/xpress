<?php

namespace common\repositories;

use common\dispatchers\EventDispatcher;
use common\models\Subject;
use yii\db\ActiveQuery;

class SubjectRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get($id): Subject
    {
        return $this->getBy(['subject_uuid' => $id]);
    }

    public function dispatch(Subject $subject): void
    {
        $this->dispatcher ? $this->dispatcher->dispatchAll($subject->releaseEvents()) : '';
    }

    public function getByUuid(array $uuids): ActiveQuery
    {
        return Subject::find()->andWhere(['IN', 'subject_uuid', $uuids]);
    }

    private function getBy(array $condition): Subject
    {
        if (!$subject = Subject::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Цель не найдена');
        }
        return $subject;
    }
}
