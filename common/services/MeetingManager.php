<?php

namespace common\services;

use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\HooksCreateParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use common\components\BigBlueButton;
use common\models\Meeting;
use common\models\TrainingSession;
use DomainException;
use Yii;

class MeetingManager
{
    public $manager;
    protected $attendeePW = 'attendeePW';
    protected $moderatorPW = 'moderatorPW';
    protected $hookUrl;
    protected $logoutUrl;
    protected $title = 'Видео-сессия';
    protected $welcomeMessage = 'Добро пожаловать на видео-сессию!';

    public function __construct()
    {
        $this->manager = new BigBlueButton(getenv('BBB_SERVER_BASE_URL'), getenv('BBB_SECRET'));
        $this->hookUrl = getenv('BBB_HOOK_URL');
        $this->logoutUrl = getenv('BBB_LOGOUT_URL');
    }

    /**
     * @param TrainingSession $session
     * @param string $title
     */
    public function createMeeting(TrainingSession $session, string $title): void
    {
        //        $createMeetingParams = new CreateMeetingParameters(
        //            $session->training_uuid,
        //            $session->subject->title ?? $this->title
        //        );
        //        $createMeetingParams->setAttendeePassword($this->attendeePW);
        //        $createMeetingParams->setModeratorPassword($this->moderatorPW);
        //        $createMeetingParams->setAutoStartRecording(true);
        //        $createMeetingParams->setRecord(true);
        //        $createMeetingParams->setWelcomeMessage($this->welcomeMessage);
        //        $createMeetingParams->setMeetingName($this->title);
        //        $createMeetingParams->setWebcamsOnlyForModerator(false);
        //        $createMeetingParams->setAllowStartStopRecording(true);
        //        $createMeetingParams->setEndCallbackUrl("$this->hookUrl?recordingmarks=true&meetingID={$session->training_uuid}");
        //        $createMeetingParams->setLogoutUrl($this->logoutUrl);

        /** @var CreateMeetingParameters $createMeetingParams */
        $createMeetingParams = $this->prepareMeetingParams($session->training_uuid, $title);

        $response = $this->manager->createMeeting($createMeetingParams);
        if ($response->getReturnCode() === 'FAILED') {
            Yii::error($response->getMessage());
            throw new DomainException('Can\'t create room! please contact our administrator.');
        }
    }

    /**
     * @param Meeting $meeting
     */
    public function createGroupMeeting(Meeting $meeting): void
    {
        /** @var CreateMeetingParameters $createMeetingParams */
        $createMeetingParams = $this->prepareMeetingParams($meeting->meeting_uuid, $meeting->title, true);
        $createMeetingParams->setMaxParticipants(\Yii::$app->params['BBB_MAX_PARTICIPANTS']);

        $response = $this->manager->createMeeting($createMeetingParams);
        if ($response->getReturnCode() === 'FAILED') {
            Yii::error($response->getMessage());
            throw new DomainException('Can\'t create room! please contact our administrator.');
        }
    }

    /**
     * @param $uuid
     * @param $title
     * @return CreateMeetingParameters
     */
    private function prepareMeetingParams($uuid, $title, $isGroup = false)
    {
        $createMeetingParams = new CreateMeetingParameters(
            $uuid,
            $title ?? $this->title
        );
        $createMeetingParams->setAttendeePassword($this->attendeePW);
        $createMeetingParams->setModeratorPassword($this->moderatorPW);
        $createMeetingParams->setAutoStartRecording(true);
        $createMeetingParams->setRecord(true);
        $createMeetingParams->setWelcomeMessage($this->welcomeMessage);
        $createMeetingParams->setMeetingName($this->title);
        $createMeetingParams->setWebcamsOnlyForModerator(false);
        $createMeetingParams->setAllowStartStopRecording(true);
        $createMeetingParams->setEndCallbackUrl($this->hookUrl . ($isGroup ? 'end-meeting' : 'end') . '?recordingmarks=true&meetingId=' . $uuid);
        $createMeetingParams->setLogoutUrl(($isGroup ? str_replace('/rating', '', $this->logoutUrl) . '/from_bbb' : $this->logoutUrl));

        return $createMeetingParams;
    }

    /**
     * Присоединиться к вебинару не сотрудинику
     * @param string $uuid
     * @param string $name
     * @return string
     */
    public function joinMdMeeting(string $uuid, string $name)
    {
        $joinParams = new JoinMeetingParameters(
            $uuid, $name, $this->moderatorPW
        );

        $joinParams->setRedirect(false);
        $joinResponse = $this->manager->joinMeeting($joinParams);

        if ($joinResponse->getReturnCode() === 'FAILED') {
            Yii::error($joinResponse->getMessage());
            throw new DomainException('Can\'t join room! please contact our administrator.');
        }

        return $joinResponse->getUrl();
    }

    /**
     * Присоединиться к вебинару сотрудинику
     * @param string $uuid
     * @param string $name
     * @return string
     */
    public function joinAtMeeting(string $uuid, string $name)
    {
        $joinParams = new JoinMeetingParameters(
            $uuid, $name, $this->attendeePW
        );
        $joinParams->setRedirect(false);
        $joinResponse = $this->manager->joinMeeting($joinParams);

        if ($joinResponse->getReturnCode() === 'FAILED') {
            Yii::error($joinResponse->getMessage());
            throw new DomainException('Can\'t join room! please contact our administrator.');
        }

        return $joinResponse->getUrl();
    }

    public function createHook()
    {
        $hookParams = new HooksCreateParameters($this->hookUrl . 'end');
        $response = $this->manager->hooksCreate($hookParams);
        Yii::error($response);
        if ($response->getReturnCode() !== 'SUCCESS') {
            Yii::error($response->getMessage());
            throw new DomainException($response->getMessage());
        }
        return ['hook_id' => $response->getHookId(), 'message' => $response->getMessage()];
    }

    public function getRecords(TrainingSession $session)
    {
        $recParams = new GetRecordingsParameters();
        $recParams->setMeetingId($session->training_uuid);
        $response = $this->manager->getRecordings($recParams);

        if ($response->getReturnCode() === 'FAILED') {
            Yii::error($response->getMessage());
            throw new DomainException('Can\'t create room! please contact our administrator.');
        }

        return $response->getRecords();
    }
}