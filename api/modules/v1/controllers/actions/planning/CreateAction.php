<?php

namespace api\modules\v1\controllers\actions\planning;

use api\modules\v1\controllers\HelperTrait;
use common\forms\training\TrainingCreateForm;
use common\models\Program;
use common\models\TrainingSession;
use common\models\User;
use common\useCases\TrainingManageCase;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class CreateAction extends \yii\rest\CreateAction
{
    use HelperTrait;

    public $useCase;

    public function __construct($id, $controller, TrainingManageCase $useCase, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->useCase = $useCase;
    }

    /**
     * Добавление новой сессии
     * @return TrainingCreateForm|TrainingSession|\yii\db\ActiveRecordInterface
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $form = new TrainingCreateForm($user);
        $form->scenario = $user->isUserRoleEmployee() ? 'default' : 'free';

        if ($this->validateBody($form)) {
            try {
                $mentorOrCoachUser = $user;

                if (!empty($form->invited_uuid)) {
                    /** @var User $invitedUser */
                    $invitedUser = User::findOne($form->invited_uuid);

                    if ($invitedUser->isUserRoleCoach() || $invitedUser->isUserRoleMentor()) {
                        $programUuid = $invitedUser->isUserRoleCoach() ? Program::COACH_UUID : Program::MENTOR_UUID;
                        $user->checkingEmployeeForLimitPlannedSession($programUuid);
                        /** @var User $mentorOrCoachUser */
                        $mentorOrCoachUser = $invitedUser;
                        /** @var User $employeeUser */
                        $employeeUser = $user;
                    } else {
                        /** @var User $employeeUser */
                        $employeeUser = $invitedUser;
                    }
                    if (!$employeeUser->isSessionTimeFree($form->start_at, $form->duration)) {
                        $errorMessage = $employeeUser->getEmployeeSessionBusyTimeErrorMessage();
                        throw new ForbiddenHttpException($errorMessage);
                    }
                }

                if ($form->scenario === 'free') {
                    if (!$mentorOrCoachUser->isFreeSessionTimeFree($form->start_at, $form->duration)) {
                        $errorMessage = $mentorOrCoachUser->getMentorFreeSessionBusyTimeErrorMessage();
                        throw new ForbiddenHttpException($errorMessage);
                    }
                }

                if (!$mentorOrCoachUser->isSessionTimeFree($form->start_at, $form->duration)) {
                    $errorMessage = $mentorOrCoachUser->getMentorSessionBusyTimeErrorMessage();
                    throw new ForbiddenHttpException($errorMessage);
                }

                $model = $this->useCase->create($form);
                \Yii::$app->response->setStatusCode(201);
                $model->refresh();
                return $model;
            } catch (\DomainException $e) {
                throw new BadRequestHttpException($e->getMessage(), null, $e);
            }
        }
        return $form;
    }
}
