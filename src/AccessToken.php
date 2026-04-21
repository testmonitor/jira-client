<?php

namespace TestMonitor\Jira;

use League\OAuth2\Client\Token\AccessToken as OAuth2AccessToken;

class AccessToken
{
    public string $accessToken;

    public string $refreshToken;

    public int $expiresIn;

    /**
     * Create a new access token instance.
     */
    public function __construct(string $accessToken = '', string $refreshToken = '', int $expiresIn = 0)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
    }

    /**
     * Create an access token from a Jira OAuth token.
     */
    public static function fromJira(OAuth2AccessToken $token): self
    {
        return new self(
            $token->getToken(),
            $token->getRefreshToken(),
            $token->getExpires()
        );
    }

    /**
     * Determines if the access token has expired.
     */
    public function expired(): bool
    {
        return ($this->expiresIn - time()) < 60;
    }

    /**
     * Returns the token as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in' => $this->expiresIn,
        ];
    }

    /**
     * Returns the token as a Jira OAuth token.
     */
    public function toJira(): OAuth2AccessToken
    {
        return new OAuth2AccessToken([
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in' => $this->expiresIn,
        ]);
    }
}
