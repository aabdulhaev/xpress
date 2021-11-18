<?php

namespace common\components;

use common\components\google\Token;
use common\components\google\Auth;
use common\components\google\Calandar;
use Google_Client;
use Google_Service_Calendar;
use Yii;
use yii\base\Component;

/**
 *
 * @property-read Auth $auth
 */
class Google extends Component
{
    /**
     * @var string
     */
    public $authConfigPath;

    /**
     * @var string
     */
    public $redirectUri;

    /**
     * @var Google_Client
     */
    private $client;

    public function __construct(Google_Client $client, $config = [])
    {
        parent::__construct($config);
        $this->client = $client;
        $client->setAuthConfig($this->authConfigPath);
        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
    }

    public function getCalendar(Token $accessToken): Calandar
    {
        return new Calandar($this->client, $accessToken);
    }

    public function getAuth(): Auth
    {
        return new Auth($this->client, $this->redirectUri);
    }

}