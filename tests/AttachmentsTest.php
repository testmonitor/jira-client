<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use TestMonitor\Jira\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Resources\Attachment;
use TestMonitor\Jira\Exceptions\NotFoundException;
use TestMonitor\Jira\Exceptions\ValidationException;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;

class AttachmentsTest extends TestCase
{
    protected $token;

    protected $project;

    protected $issue;

    protected $attachment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Mockery::mock('\TestMonitor\Jira\AccessToken');
        $this->token->shouldReceive('expired')->andReturnFalse();

        $this->project = ['id' => '1', 'name' => 'Project'];
        $this->issue = ['id' => 1];
        $this->attachment = ['id' => 1, 'filename' => 'logo.png', 'size' => 100, 'mimeType' => 'jpeg'];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_add_an_attachment_to_an_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        // Second, adding the attachment to the issue
        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([$this->attachment])));

        // When
        $attachments = $jira->addAttachmentToIssue($this->issue['id'], __DIR__ . '/files/logo.png');

        // Then
        $this->assertIsArray($attachments);
        $this->assertInstanceOf(Attachment::class, $attachments[0]);
        $this->assertIsArray($attachments[0]->toArray());
    }

    /** @test */
    public function it_should_retrieve_an_attachment()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        // Second, adding the attachment to the issue
        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'image/jpg'], 'foobar'));

        // When
        $attachment = $jira->attachment('1');

        // Then
        $this->assertEquals('foobar', $attachment);
    }

    /** @test */
    public function it_should_throw_an_failed_action_exception_when_client_receives_bad_request_while_adding_an_attachment_to_a_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(400, ['Content-Type' => 'application/json'], null));

        $this->expectException(FailedActionException::class);

        // When
        $jira->addAttachmentToIssue($this->issue['id'], __DIR__ . '/files/logo.png');
    }

    /** @test */
    public function it_should_throw_a_notfound_exception_when_client_receives_not_found_while_adding_an_attachment_to_a_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(404, ['Content-Type' => 'application/json'], null));

        $this->expectException(NotFoundException::class);

        // When
        $jira->addAttachmentToIssue($this->issue['id'], __DIR__ . '/files/logo.png');
    }

    /** @test */
    public function it_should_throw_an_unauthorized_exception_when_client_lacks_authorization_for_adding_an_attachment_to_a_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(401, ['Content-Type' => 'application/json'], null));

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->addAttachmentToIssue($this->issue['id'], __DIR__ . '/files/logo.png');
    }

    /** @test */
    public function it_should_throw_a_validation_exception_when_client_provides_invalid_data_while_adding_an_attachment_to_a_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['message' => 'invalid'])));

        $this->expectException(ValidationException::class);

        // When
        $jira->addAttachmentToIssue($this->issue['id'], __DIR__ . '/files/logo.png');
    }
}
