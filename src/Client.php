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
    use Actions\ManagesAccounts;
    use Actions\ManagesAttachments;
    use Actions\ManagesIssues;
    use Actions\ManagesIssuePriorities;
    use Actions\ManagesIssueStatuses;
    use Actions\ManagesIssueTypes;
    use Actions\ManagesProjects;
    use Actions\ManagesProjectVersions;
    use Actions\ManagesUsers;
    use Actions\ManagesWebhooks;

    protected ?AccessToken $token;

    protected string $cloudId;

    protected string $baseUrl = 'https://api.atlassian.com/ex/jira';

    protected string $apiVersion = '3';

    protected ?\GuzzleHttp\Client $client = null;

    protected JiraProvider $provider;

    /**
     * oAuth scopes.
     */
    protected array $scopes = [
        'manage:jira-configuration',
        'manage:jira-webhook',
        'read:jira-user',
        'read:jira-work',
        'write:jira-work',
        'offline_access',
    ];

    /**
     * Create a new client instance.
     */
    public function __construct(
        array $credentials,
        string $cloudId = '',
        ?AccessToken $token = null,
        ?JiraProvider $provider = null
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
     */
    public function authorizationUrl(string $state): string
    {
        return $this->provider->getAuthorizationUrl([
            'state' => $state,
            'scope' => $this->scopes,
        ]);
    }

    /**
     * Fetch the access and refresh token based on the authorization code.
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
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     */
    public function refreshToken(): AccessToken
    {
        if (empty($this->token)) {
            throw new UnauthorizedException('Invalid access token');
        }

        try {
            $token = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->token->refreshToken,
                'scope' => $this->scopes,
            ]);

            $this->token = AccessToken::fromJira($token);
        } catch (JiraIdentityProviderException $exception) {
            throw new UnauthorizedException((string) $exception->getResponseBody(), $exception->getCode(), $exception);
        }

        return $this->token;
    }

    /**
     * Determines if the current access token has expired.
     */
    public function tokenExpired(): bool
    {
        return $this->token->expired();
    }

    /**
     * Set the Guzzle HTTP client instance.
     */
    public function setClient(\GuzzleHttp\Client $client): void
    {
        $this->client = $client;
    }

    /**
     * Returns a Guzzle client instance.
     *
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     */
    protected function client(): \GuzzleHttp\Client
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
     * Make a GET request to Jira servers and return the response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     */
    protected function get(string $uri, array $payload = []): mixed
    {
        return $this->request('GET', $uri, $payload);
    }

    /**
     * Make a POST request to Jira servers and return the response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     */
    protected function post(string $uri, array $payload = []): mixed
    {
        return $this->request('POST', $uri, $payload);
    }

    /**
     * Make a PUT request to Jira servers and return the response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     */
    protected function put(string $uri, array $payload = []): mixed
    {
        return $this->request('PUT', $uri, $payload);
    }

    /**
     * Make a DELETE request to Jira servers and return the response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     */
    protected function delete(string $uri, array $payload = []): mixed
    {
        return $this->request('DELETE', $uri, $payload);
    }

    /**
     * Make a request to Jira servers and return the response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\TokenExpiredException
     * @throws \TestMonitor\Jira\Exceptions\UnauthorizedException
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     */
    protected function request(string $verb, string $uri, array $payload = []): mixed
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
     * @throws \TestMonitor\Jira\Exceptions\ValidationException
     * @throws \TestMonitor\Jira\Exceptions\NotFoundException
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     * @throws \Exception
     */
    protected function handleRequestError(ResponseInterface $response): never
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
