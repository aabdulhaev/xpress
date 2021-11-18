<?php

namespace api\modules\v1\controllers\actions\planning;

use api\modules\v1\controllers\HelperTrait;
use common\forms\training\TrainingSearchForm;
use common\repositories\TrainingRepository;
use yii\data\ActiveDataProvider;

class IndexAction extends \yii\rest\IndexAction
{
    use HelperTrait;

    private $trainingRepo;

    public function __construct($id, $controller, TrainingRepository $trainingRepo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->trainingRepo = $trainingRepo;
    }

    /**
     * Получение информации о сессиях
     *
     * @return TrainingSearchForm|ActiveDataProvider
     */
    public function run()
    {
        $form = new TrainingSearchForm();
        if ($this->validateQuery($form)) {
            return $this->trainingRepo->search($form);
        }

        return $form;
    }
}
