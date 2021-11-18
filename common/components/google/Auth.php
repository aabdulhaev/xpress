<?php

namespace common\components\google;

use DomainException;
use Google_Client;

class Auth
{
    /**
     * @var Google_Client
     */
    private $client;

    public function __construct(Google_Client $client, string $redirectUri)
    {
        $this->client = $client;
        $this->client->setRedirectUri($redirectUri);
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function getAccessTokenByCode(string $code): Token
    {
        $this->client->fetchAccessTokenWithAuthCode($code);
        $accessToken = $this->client->getAccessToken();
        if (!isset($accessToken['access_token'])) {
            throw new DomainException('Access token empty');
        }
        return new Token(
            $accessToken['access_token'],
            $accessToken['created'] ?? time(),
            $accessToken['expires_in'] ?? 0,
            $accessToken['refresh_token'] ?? null
        );
    }

    public function updateToken(Token $oldToken): Token
    {
        $this->client->fetchAccessTokenWithRefreshToken($oldToken->getRefreshToken());
        $refreshTokenSaved = $this->client->getRefreshToken();
        $tokenUpdated = $this->client->getAccessToken();
        $tokenUpdated['refresh_token'] = $refreshTokenSaved;
        $this->client->setAccessToken($tokenUpdated);

        if (!isset($tokenUpdated['access_token'])) {
            throw new DomainException('Access token empty');
        }

        return new Token(
            $tokenUpdated['access_token'],
            $tokenUpdated['created'] ?? time(),
            $tokenUpdated['expires_in'] ?? 0,
            $tokenUpdated['refresh_token'] ?? null
        );
    }
}