<?php

declare(strict_types=1);

namespace common\useCases;

use common\forms\RequestForm;
use common\models\Request;
use common\repositories\RequestRepository;

class RequestCase
{

    protected $repo;

    public function __construct(RequestRepository $repo)
    {
        $this->repo = $repo;
    }

    public function create(RequestForm $form): Request
    {
        $model = Request::create($form->name, $form->email, $form->phone, $form->description, $form->type);
        $this->repo->save($model);
        return $model;
    }

    public function approve(Request $model): void
    {
        $model->edit(
            Request::STATUS_APPROVED
        );

        $this->repo->save($model);
    }

    public function decline(Request $model): void
    {
        $model->edit(
            Request::STATUS_DECLINE
        );

        $this->repo->save($model);
    }

}
