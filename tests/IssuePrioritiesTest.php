<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use TestMonitor\Jira\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Resources\IssuePriority;
use TestMonitor\Jira\Exceptions\NotFoundException;
use TestMonitor\Jira\Exceptions\ValidationException;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;

class IssuePrioritiesTest extends TestCase
{
    protected $token;

    protected $issuePriority;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Mockery::mock('\TestMonitor\Jira\AccessToken');
        $this->token->shouldReceive('expired')->andReturnFalse();

        $this->issuePriority = ['id' => '1', 'name' => 'Issue Status'];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    #[Test]
    public function it_should_return_a_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode(['values' => [$this->issuePriority]])));

        // When
        $issuePriorities = $jira->issuePriorities('123456789');

        // Then
        $this->assertIsArray($issuePriorities);
        $this->assertCount(1, $issuePriorities);
        $this->assertInstanceOf(IssuePriority::class, $issuePriorities[0]);
        $this->assertEquals($this->issuePriority['id'], $issuePriorities[0]->id);
        $this->assertIsArray($issuePriorities[0]->toArray());
    }

    #[Test]
    public function it_should_throw_a_failed_action_exception_when_client_receives_bad_request_while_getting_a_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(400, ['Content-Type' => 'application/json'], null));

        $this->expectException(FailedActionException::class);

        // When
        $jira->issuePriorities('123456789');
    }

    #[Test]
    public function it_should_throw_a_notfound_exception_when_client_receives_not_found_while_getting_a_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(404, ['Content-Type' => 'application/json'], null));

        $this->expectException(NotFoundException::class);

        // When
        $jira->issuePriorities('123456789');
    }

    #[Test]
    public function it_should_throw_an_unauthorized_exception_when_client_lacks_authorization_for_getting_a_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(401, ['Content-Type' => 'application/json'], null));

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->issuePriorities('123456789');
    }

    #[Test]
    public function it_should_throw_a_validation_exception_when_client_provides_invalid_data_while_getting_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['message' => 'invalid'])));

        $this->expectException(ValidationException::class);

        // When
        $jira->issuePriorities('123456789');
    }

    #[Test]
    public function it_should_return_an_error_message_when_client_provides_invalid_data_while_getting_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['errors' => ['invalid']])));

        // When
        try {
            $jira->issuePriorities('123456789');
        } catch (ValidationException $exception) {
            // Then
            $this->assertIsArray($exception->errors());
            $this->assertEquals('invalid', $exception->errors()['errors'][0]);
        }
    }

    #[Test]
    public function it_should_throw_a_generic_exception_when_client_suddenly_becomes_a_teapot_while_getting_list_of_issue_priorities()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(418, ['Content-Type' => 'application/json'], json_encode(['rooibos' => 'anyone?'])));

        $this->expectException(Exception::class);

        // When
        $jira->issuePriorities('123456789');
    }
}
