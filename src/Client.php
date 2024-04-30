<?php

namespace TestMonitor\Jira;

use Psr\Http\Message\ResponseInterface;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Exceptions\NotFoundException;
use TestMonitor\Jira\Exceptions\ValidationException;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Exceptions\TokenExpiredException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;
use Mrjoops\OAuth2\Client\Provider\Jira as JiraProvider;
use Mrjoops\OAuth2\Client\Provider\Exception\JiraIdentityProviderException;

class Client
{
    use Actions\ManagesAccounts,
        Actions\ManagesAttachments,
        Actions\ManagesIssues,
        Actions\ManagesIssuePriorities,
        Actions\ManagesIssueStatuses,
        Actions\ManagesIssueTypes,
        Actions\ManagesProjects,
        Actions\ManagesProjectVersions,
        Actions\ManagesUsers,
        Actions\ManagesWebhooks;

    /**
     * @var \TestMonitor\Jira\AccessToken
     */
    protected $token;

    /**
     * @var string
     */
    protected $cloudId;

    /**
     * @var string
     */
    protected $baseUrl = 'https://api.atlassian.com/ex/jira';

    /**
     * @var string
     */
    protected $apiVersion = '3';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Mrjoops\OAuth2\Client\Provider\Jira
     */
    protected $provider;

    /**
     * oAuth scopes.
     *
     * @var array
     */
    protected $scopes = [
        'manage:jira-configuration',
        'manage:jira-webhook',
        'read:jira-user',
        'read:jira-work',
        'write:jira-work',
        'offline_access',
    ];

    /**
     * Create a new client instance.
     *
     * @param array $credentials
     * @param string $cloudId
     * @param \TestMonitor\Jira\AccessToken $token
     * @param \Mrjoops\OAuth2\Client\Provider\Jira $provider
     */
    public function __construct(
        array $credentials,
        string $cloudId = '',
        AccessToken $token = null,
        $provider = null
    ) {
        $this->token = $token;
        $this->cloudId = $cloudId;

        $this->provider = $provider ?? new JiraProvider([
            'clientId' => $credentials['clientId'],
            'clientSecret' => $credentials['clientSecret'],
            'redirectUri' => $credentials['redirectUrl'],
        ]);
    }

    /**
     * Create a new authorization URL for the given state.
     *
     * @param string $state
     * @return string
     */
    public function authorizationUrl($state)
    {
        return $this->provider->getAuthorizationUrl([
            'state' => $state,
            'scope' => $this->scopes,
        ]);
    }

    /**
     * Fetch the access and refresh token based on the authorization code.
     *
     * @param string $code
     * @return \TestMonitor\Jira\AccessToken
     */
    public function fetchToken(string $code): AccessToken
    {
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        $this->token = AccessToken::fromJira($token);

        return $this->token;
    }

    /**
     * Refresh the current access token.
     *
     * @throws \Exception
     *
     * @return \TestMonitor\Jira\AccessToken
     */
    public function refreshToken(): AccessToken
    {
        if (empty($this->token)) {
            throw new UnauthorizedException('Invalid access token');
        }

        try {
            $token = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->token->refreshToken,
            ]);

            $this->token = AccessToken::fromJira($token);
        } catch (JiraIdentityProviderException $exception) {
            throw new UnauthorizedException((string) $exception->getResponseBody(), $exception->getCode(), $exception);
        }

        return $this->token;
    }

    /**
     * Determines if the current access token has expired.
     *
     * @return bool
     */
    public function tokenExpired()
    {
        return $this->token->expired();
    }

    /**
     * Returns an Guzzle client instance.
     *
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     *
     * @return \GuzzleHttp\Client
     */
    protected function client()
    {
        if (empty($this->token)) {
            throw new UnauthorizedException();
        }

        if ($this->token->expired()) {
            throw new TokenExpiredException();
        }

        return $this->client ?? new \GuzzleHttp\Client([
            'base_uri' => "{$this->baseUrl}/{$this->cloudId}/rest/api/{$this->apiVersion}/",
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token->accessToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * @param \GuzzleHttp\Client $client
     */
    public function setClient(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Make a GET request to Jira servers and return the response.
     *
     * @param string $uri
     * @param array $payload
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     *
     * @return mixed
     */
    protected function get($uri, array $payload = [])
    {
        return $this->request('GET', $uri, $payload);
    }

    /**
     * Make a POST request to Jira servers and return the response.
     *
     * @param string $uri
     * @param array $payload
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     *
     * @return mixed
     */
    protected function post($uri, array $payload = [])
    {
        return $this->request('POST', $uri, $payload);
    }

    /**
     * Make a PUT request to Forge servers and return the response.
     *
     * @param string $uri
     * @param array $payload
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     *
     * @return mixed
     */
    protected function put($uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    /**
     * Make a DELETE request to Jira servers and return the response.
     *
     * @param string $uri
     * @param array $payload
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     *
     * @return mixed
     */
    protected function delete($uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }

    /**
     * Make request to Jira servers and return the response.
     *
     * @param string $verb
     * @param string $uri
     * @param array $payload
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     *
     * @return mixed
     */
    protected function request($verb, $uri, array $payload = [])
    {
        $response = $this->client()->request(
            $verb,
            $uri,
            $payload
        );

        if (! in_array($response->getStatusCode(), [200, 201, 202, 203, 204, 206])) {
            return $this->handleRequestError($response);
        }

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    /**
     * @param  \Psr\Http\Message\ResponseInterface $response
     *
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \Exception
     *
     * @return void
     */
    protected function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 422) {
            throw new ValidationException(json_decode((string) $response->getBody(), true));
        }

        if ($response->getStatusCode() == 404) {
            throw new NotFoundException((string) $response->getBody(), $response->getStatusCode());
        }

        if ($response->getStatusCode() == 401 || $response->getStatusCode() == 403) {
            throw new UnauthorizedException((string) $response->getBody(), $response->getStatusCode());
        }

        if ($response->getStatusCode() == 400) {
            throw new FailedActionException((string) $response->getBody(), $response->getStatusCode());
        }

        throw new Exception((string) $response->getStatusCode());
    }
}
