<?php

namespace common\components\google;

use DomainException;

class Token
{
    private $accessToken;
    private $created;
    private $expiresIn;
    private $refreshToken;

    public function __construct(?string $accessToken, int $created, int $expiresIn, ?string $refreshToken = null)
    {
        $this->accessToken = $accessToken;
        $this->created = $created;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
    }

    public function issetAccessToken(): bool
    {
        return $this->accessToken !== null;
    }

    public function verify(): void
    {
        if (time() > ($this->created + $this->expiresIn)) {
            throw new DomainException('Token expire');
        }
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }
}