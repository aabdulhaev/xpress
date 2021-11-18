<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use common\filters\Cors;
use common\forms\TrainingRatingForm;
use common\models\User;
use common\models\UserTraining;
use common\repositories\MeetingRepository;
use common\repositories\TrainingRepository;
use common\repositories\UserRepository;
use common\repositories\UserTrainingRepository;
use common\useCases\TrainingManageCase;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class HookController extends Controller
{
    use HelperTrait;

    public $modelClass = User::class;
    public $useCase;
    public $trainingRepo;
    public $userRepo;
    public $assignmentsRepo;
    public $meetingRepo;

    public function __construct(
        $id,
        $module,
        TrainingManageCase $useCase,
        UserRepository $userRepo,
        TrainingRepository $trainingRepo,
        UserTrainingRepository $assignmentsRepo,
        MeetingRepository $meetingRepo,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->trainingRepo = $trainingRepo;
        $this->userRepo = $userRepo;
        $this->useCase = $useCase;
        $this->assignmentsRepo = $assignmentsRepo;
        $this->meetingRepo = $meetingRepo;
    }

    protected function verbs()
    {
        return [
            'bbb' => ['POST', 'OPTIONS'],
            'end' => ['GET', 'OPTIONS'],
            'end-meeting' => ['GET', 'OPTIONS'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = Cors::class;

        return $behaviors;
    }

    /**
     * webHook
     */
    public function actionBbb($meetingID)
    {
        $data = \Yii::$app->request->bodyParams;
        $training_uuid = ArrayHelper::getValue($data, 'data.attributes.meeting.external-meeting-id');

        \Yii::error(['data' => $data, 'tu' => $training_uuid]);

        return $data;
    }

    /**
     * webHook
     */
    public function actionEnd($meetingId)
    {
        $session = $this->trainingRepo->get($meetingId);
        $coach = $session->coachOrMentor;
        $form = new TrainingRatingForm($coach, $session);

        if ($this->validateBody($form)) {
            try {
                /** @var UserTraining $assignment */
                $assignment = $this->assignmentsRepo->get($coach->user_uuid, $session->training_uuid);
                /** @var UserTraining $assignmentOther */
                $assignmentOther = $this->assignmentsRepo->getOther($coach->user_uuid, $session->training_uuid);

                $this->useCase->complete($form, $assignment, $assignmentOther);
                \Yii::$app->response->setStatusCode(201);
                return 'OK';
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }

    public function actionEndMeeting($meetingId)
    {
        $meeting = $this->meetingRepo->get($meetingId);
        $meeting->toComplete();

        $this->meetingRepo->save($meeting);
    }
}
