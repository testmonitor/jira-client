<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use TestMonitor\Jira\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Resources\Account;
use TestMonitor\Jira\Exceptions\NotFoundException;
use TestMonitor\Jira\Exceptions\ValidationException;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;

class AccountsTest extends TestCase
{
    protected $token;

    protected $account;

    protected $tenantInfo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Mockery::mock('\TestMonitor\Jira\AccessToken');
        $this->token->shouldReceive('expired')->andReturnFalse();

        $this->account = [
            'id' => 1,
            'url' => 'https://my.jira.account',
            'name' => 'Harry the Horse',
            'scopes' => ['do-this'],
            'avatarUrl' => 'https://my.jira.avatar',
        ];

        $this->tenantInfo = ['cloudId' => '12345-abcde-12345-abcde'];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_a_list_of_accounts()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                $this->account,
            ])));

        // When
        $accounts = $jira->accounts();

        // Then
        $this->assertIsArray($accounts);
        $this->assertCount(1, $accounts);
        $this->assertInstanceOf(Account::class, $accounts[0]);
        $this->assertEquals($this->account['id'], $accounts[0]->id);
    }

    /** @test */
    public function it_should_throw_an_failed_action_exception_when_client_receives_bad_request_while_getting_a_list_of_accounts()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(400, ['Content-Type' => 'application/json'], null));

        $this->expectException(FailedActionException::class);

        // When
        $jira->accounts();
    }

    /** @test */
    public function it_should_throw_a_notfound_exception_when_client_receives_not_found_while_getting_a_list_of_accounts()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(404, ['Content-Type' => 'application/json'], null));

        $this->expectException(NotFoundException::class);

        // When
        $jira->accounts();
    }

    /** @test */
    public function it_should_throw_an_unauthorized_exception_when_client_lacks_authorization_for_getting_a_list_of_accounts()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(401, ['Content-Type' => 'application/json'], null));

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->accounts();
    }

    /** @test */
    public function it_should_throw_a_validation_exception_when_client_provides_invalid_data_while_getting_list_of_accounts()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['message' => 'invalid'])));

        $this->expectException(ValidationException::class);

        // When
        $jira->accounts();
    }

    /** @test */
    public function it_should_return_your_cloud_id()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode(
                $this->tenantInfo
            )));

        // When
        $cloudId = $jira->cloudId('https://my.jira.url');

        // Then
        $this->assertIsString($cloudId);
        $this->assertEquals('12345-abcde-12345-abcde', $cloudId);
    }

    /** @test */
    public function it_should_throw_an_unauthorized_exception_when_client_lacks_authorization_for_getting_your_cloud_id()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(401, ['Content-Type' => 'application/json'], null));

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->cloudId('https://my.jira.url');
    }
}
