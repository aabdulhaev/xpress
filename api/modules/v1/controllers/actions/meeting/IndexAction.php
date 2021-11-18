<?php

namespace api\modules\v1\controllers\actions\meeting;

use api\modules\v1\controllers\HelperTrait;
use common\forms\meeting\MeetingSearchForm;
use common\repositories\MeetingRepository;
use yii\data\ActiveDataProvider;

/**
 * @OA\Get(
 *     path="/meeting/index",
 *     tags={"Meeting"},
 *     summary="Возвращает список групповых вебинаров.",
 *     @OA\RequestBody(
 *         description="Параметры",
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(ref="#/components/schemas/MeetingSearchForm")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Список вебинаров",
 *         @OA\JsonContent(ref="#/components/schemas/Meeting")),
 *     ),
 * )
 */
class IndexAction extends \yii\rest\IndexAction
{
    use HelperTrait;

    private $meetingRepo;

    public function __construct($id, $controller, MeetingRepository $meetingRepo, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->meetingRepo = $meetingRepo;
    }

    /**
     * Получение информации о сессиях
     *
     * @return MeetingSearchForm|ActiveDataProvider
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $form = new MeetingSearchForm();
        if ($this->validateQuery($form)) {
            return $this->meetingRepo->search($form);
        }

        return $form;
    }
}
