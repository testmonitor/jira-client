<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use TestMonitor\Jira\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Resources\IssueType;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Exceptions\NotFoundException;
use TestMonitor\Jira\Exceptions\ValidationException;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;

class IssueTypesTest extends TestCase
{
    protected $token;

    protected $issueType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Mockery::mock('\TestMonitor\Jira\AccessToken');
        $this->token->shouldReceive('expired')->andReturnFalse();

        $this->issueType = ['id' => '1', 'name' => 'Issue Type'];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_a_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([$this->issueType])));

        // When
        $issueTypes = $jira->issueTypes('123456789');

        // Then
        $this->assertIsArray($issueTypes);
        $this->assertCount(1, $issueTypes);
        $this->assertInstanceOf(IssueType::class, $issueTypes[0]);
        $this->assertEquals($this->issueType['id'], $issueTypes[0]->id);
        $this->assertIsArray($issueTypes[0]->toArray());
    }

    /** @test */
    public function it_should_throw_an_failed_action_exception_when_client_receives_bad_request_while_getting_a_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(400, ['Content-Type' => 'application/json'], null));

        $this->expectException(FailedActionException::class);

        // When
        $jira->issueTypes('123456789');
    }

    /** @test */
    public function it_should_throw_a_notfound_exception_when_client_receives_not_found_while_getting_a_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(404, ['Content-Type' => 'application/json'], null));

        $this->expectException(NotFoundException::class);

        // When
        $jira->issueTypes('123456789');
    }

    /** @test */
    public function it_should_throw_an_unauthorized_exception_when_client_lacks_authorization_for_getting_a_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(401, ['Content-Type' => 'application/json'], null));

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->issueTypes('123456789');
    }

    /** @test */
    public function it_should_throw_a_validation_exception_when_client_provides_invalid_data_while_getting_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['message' => 'invalid'])));

        $this->expectException(ValidationException::class);

        // When
        $jira->issueTypes('123456789');
    }

    /** @test */
    public function it_should_return_an_error_message_when_client_provides_invalid_data_while_getting_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['errors' => ['invalid']])));

        // When
        try {
            $jira->issueTypes('123456789');
        } catch (ValidationException $exception) {
            // Then
            $this->assertIsArray($exception->errors());
            $this->assertEquals('invalid', $exception->errors()['errors'][0]);
        }
    }

    /** @test */
    public function it_should_throw_a_generic_exception_when_client_suddenly_becomes_a_teapot_while_getting_list_of_issue_types()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(418, ['Content-Type' => 'application/json'], json_encode(['rooibos' => 'anyone?'])));

        $this->expectException(Exception::class);

        // When
        $jira->issueTypes('123456789');
    }
}
