<?php

namespace TestMonitor\Jira;

use League\OAuth2\Client\Token\AccessToken as OAuth2AccessToken;

class AccessToken
{
    /**
     * @var string
     */
    public $accessToken;

    /**
     * @var string
     */
    public $refreshToken;

    /**
     * @var int
     */
    public $expiresIn;

    /**
     * Token constructor.
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @param int $expiresIn
     */
    public function __construct(string $accessToken = '', string $refreshToken = '', int $expiresIn = 0)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
    }

    /**
     * @param \League\OAuth2\Client\Token\AccessToken $token
     * @return \TestMonitor\Jira\AccessToken
     */
    public static function fromJira(OAuth2AccessToken $token)
    {
        return new self(
            $token->getToken(),
            $token->getRefreshToken(),
            $token->getExpires()
        );
    }

    /**
     * Determines if the access token has expired.
     *
     * @return bool
     */
    public function expired()
    {
        return ($this->expiresIn - time()) < 60;
    }

    /**
     * Returns the token as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in' => $this->expiresIn,
        ];
    }

    /**
     * Returns the token as a Jira OAuth token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken
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
