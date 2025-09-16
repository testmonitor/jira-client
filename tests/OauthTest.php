<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use TestMonitor\Jira\Client;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\AccessToken;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Jira\Exceptions\TokenExpiredException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;

class OauthTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    #[Test]
    public function it_should_create_a_token()
    {
        // When
        $token = new AccessToken('12345', '67890', time() + 3600);

        // Then
        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertIsArray($token->toArray());
        $this->assertFalse($token->expired());
    }

    #[Test]
    public function it_should_detect_an_expired_token()
    {
        // Given
        $token = new AccessToken('12345', '67890', time() - 60);
        $cloudId = 'xxxxxxx';

        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $cloudId, $token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        // When
        $expired = $jira->tokenExpired();

        // Then
        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertTrue($token->expired());
        $this->assertTrue($expired);
    }

    #[Test]
    public function it_should_not_provide_a_client_with_an_expired_token()
    {
        // Given
        $token = new AccessToken('12345', '67890', time() - 60);
        $cloudId = 'xxxxxxx';

        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $cloudId, $token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $this->expectException(TokenExpiredException::class);

        // When
        $jira = $jira->accounts();
    }

    #[Test]
    public function it_should_provide_an_authorization_url()
    {
        // Given
        $cloudId = 'xxxxxxx';
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $cloudId, new AccessToken());

        $state = 'somestate';

        $expectedUrl = 'https://auth.atlassian.com/authorize?audience=api.atlassian.com&prompt=consent&state=somestate&scope=manage%3Ajira-configuration%20manage%3Ajira-webhook%20read%3Ajira-user%20read%3Ajira-work%20write%3Ajira-work%20offline_access&response_type=code&approval_prompt=auto&redirect_uri=none&client_id=1';

        // When
        $url = $jira->authorizationUrl($state);

        // Then
        $this->assertEquals($expectedUrl, $url);
    }

    #[Test]
    public function it_should_fetch_a_token()
    {
        // Given
        $cloudId = 'xxxxxxx';
        $code = 'somecode';

        $newToken = new \League\OAuth2\Client\Token\AccessToken(['access_token' => '23456', 'refresh_token' => 678901, 'expires_in' => time() + 3600]);

        $provider = Mockery::mock(\Mrjoops\OAuth2\Client\Provider\Jira::class);
        $provider->shouldReceive('getAccessToken')->once()->andReturn($newToken);

        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $cloudId, new AccessToken(), $provider);

        // When
        $token = $jira->fetchToken($code);

        // Then
        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertFalse($token->expired());
        $this->assertEquals($token->accessToken, $newToken->getToken());
        $this->assertEquals($token->refreshToken, $newToken->getRefreshToken());
    }

    #[Test]
    public function it_should_refresh_a_token()
    {
        // Given
        $cloudId = 'xxxxxxx';
        $oldToken = new AccessToken('12345', '567890', time() - 3600);

        $provider = Mockery::mock(\Mrjoops\OAuth2\Client\Provider\Jira::class);

        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $cloudId, $oldToken, $provider);

        $refreshToken = new \League\OAuth2\Client\Token\AccessToken(['access_token' => '23456', 'refresh_token' => 678901, 'expires_in' => time() + 3600]);

        $provider->shouldReceive('getAccessToken')->once()->andReturn($refreshToken);

        // When
        $token = $jira->refreshToken();

        // Then
        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertFalse($token->expired());
        $this->assertEquals($token->accessToken, $refreshToken->getToken());
        $this->assertEquals($token->refreshToken, $refreshToken->getRefreshToken());
    }

    #[Test]
    public function it_should_not_refresh_a_token_without_a_refresh_token()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none']);

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->refreshToken();
    }

    #[Test]
    public function it_should_not_provide_a_client_without_a_token()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none']);

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->accounts();
    }
}
