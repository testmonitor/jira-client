<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use TestMonitor\Jira\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Resources\Issue;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Jira\Resources\Project;
use TestMonitor\Jira\Resources\IssueType;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Resources\IssueStatus;
use TestMonitor\Jira\Resources\IssuePriority;
use TestMonitor\Jira\Exceptions\NotFoundException;
use TestMonitor\Jira\Exceptions\ValidationException;
use TestMonitor\Jira\Exceptions\InvalidDataException;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Exceptions\UnauthorizedException;
use TestMonitor\Jira\Responses\TokenPaginatedResponse;

class IssuesTest extends TestCase
{
    protected $token;

    protected $issue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Mockery::mock('\TestMonitor\Jira\AccessToken');
        $this->token->shouldReceive('expired')->andReturnFalse();

        $this->issue = ['id' => '1', 'key' => 'KEY', 'summary' => 'My Issue', 'description' => 'My Description'];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    #[Test]
    public function it_should_return_a_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->withArgs(function ($verb) {
                return $verb === 'GET';
            })
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'issues' => [$this->issue],
                'maxResults' => 100,
                'startAt' => 0,
                'total' => 1,
            ])));

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'POST'
                    && $uri === 'search/approximate-count';
            })
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'count' => 1,
            ])));

        // When
        $issues = $jira->issues();

        // Then
        $this->assertInstanceOf(TokenPaginatedResponse::class, $issues);
        $this->assertIsArray($issues->items());
        $this->assertCount(1, $issues->items());
        $this->assertEquals('', $issues->nextPageToken());
        $this->assertEquals(1, $issues->total());
        $this->assertEquals(50, $issues->perPage());
        $this->assertTrue($issues->isLastPage());
        $this->assertFalse($issues->hasNextPage());
        $this->assertInstanceOf(Issue::class, $issues->items()[0]);
        $this->assertEquals($this->issue['id'], $issues->items()[0]->id);
        $this->assertIsArray($issues->items()[0]->toArray());
    }

    #[Test]
    public function it_should_throw_an_failed_action_exception_when_client_receives_bad_request_while_getting_a_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(400, ['Content-Type' => 'application/json'], null));

        $this->expectException(FailedActionException::class);

        // When
        $jira->issues();
    }

    #[Test]
    public function it_should_throw_a_notfound_exception_when_client_receives_not_found_while_getting_a_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(404, ['Content-Type' => 'application/json'], null));

        $this->expectException(NotFoundException::class);

        // When
        $jira->issues();
    }

    #[Test]
    public function it_should_throw_an_unauthorized_exception_when_client_lacks_authorization_for_getting_a_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(401, ['Content-Type' => 'application/json'], null));

        $this->expectException(UnauthorizedException::class);

        // When
        $jira->issues();
    }

    #[Test]
    public function it_should_throw_a_validation_exception_when_client_provides_invalid_data_while_getting_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['message' => 'invalid'])));

        $this->expectException(ValidationException::class);

        // When
        $jira->issues();
    }

    #[Test]
    public function it_should_return_an_error_message_when_client_provides_invalid_data_while_getting_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(422, ['Content-Type' => 'application/json'], json_encode(['errors' => ['invalid']])));

        // When
        try {
            $jira->issues();
        } catch (ValidationException $exception) {
            // Then
            $this->assertIsArray($exception->errors());
            $this->assertEquals('invalid', $exception->errors()['errors'][0]);
        }
    }

    #[Test]
    public function it_should_throw_a_generic_exception_when_client_suddenly_becomes_a_teapot_while_getting_list_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(418, ['Content-Type' => 'application/json'], json_encode(['rooibos' => 'anyone?'])));

        $this->expectException(Exception::class);

        // When
        $jira->issues();
    }

    #[Test]
    public function it_should_return_the_number_of_issues()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'count' => 1,
            ])));

        // When
        $issueCount = $jira->countIssues();

        // Then
        $this->assertIsNumeric($issueCount);
        $this->assertEquals(1, $issueCount);
    }

    #[Test]
    public function it_should_return_a_single_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode($this->issue)));

        // When
        $issue = $jira->issue($this->issue['id']);

        // Then
        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertEquals($this->issue['id'], $issue->id);
        $this->assertEquals($this->issue['key'], $issue->key);
        $this->assertIsArray($issue->toArray());
    }

    #[Test]
    public function it_should_create_an_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(201, ['Content-Type' => 'application/json'], json_encode($this->issue)));

        // When
        $issue = $jira->createIssue(new Issue([
            'id' => '1',
            'key' => 'KEY',
            'summary' => 'Issue',
            'description' => 'Issue',
            'project' => new Project(['id' => 1]),
            'type' => new IssueType(['id' => 1]),
            'priority' => new IssuePriority(['id' => 1]),
            'status' => new IssueStatus(['id' => 1]),
        ]));

        // Then
        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertEquals($this->issue['id'], $issue->id);
    }

    #[Test]
    public function it_should_throw_a_validation_exception_when_client_provides_invalid_data_while_creating_an_invalid_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient(Mockery::mock('\GuzzleHttp\Client'));

        $this->expectException(InvalidDataException::class);

        // When
        $issue = $jira->createIssue(new Issue([
            'id' => '1',
            'key' => 'KEY',
            'project' => new Project(['id' => 1]),
            'type' => new IssueType(['id' => 1]),
            'priority' => new IssuePriority(['id' => 1]),
            'status' => new IssueStatus(['id' => 1]),
        ]));

        // Then
        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertEquals($this->issue['id'], $issue->id);
    }

    #[Test]
    public function it_should_get_a_html_description_of_an_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $service->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => '1',
                'key' => 'KEY',
                'summary' => 'My Issue',
                'fields' => ['description' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'My Description']],
                    ]]]],
            ])));

        // When
        $description = $jira->issue($this->issue['id'])->getDescriptionAsHTML();

        // Then
        $this->assertEquals('<div class="adf-container"><p>My Description</p></div>', $description);
    }

    #[Test]
    public function it_should_update_an_issue()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $updatedIssue = [
            'id' => '1',
            'key' => 'KEY',
            'fields' => [
                'summary' => 'Updated Issue',
                'description' => 'Updated Description',
            ],
        ];

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'PUT' && $uri === 'issue/1';
            })
            ->once()
            ->andReturn(new Response(204, ['Content-Type' => 'application/json'], null));

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'GET' && $uri === 'issue/1';
            })
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode($updatedIssue)));

        // When
        $issue = $jira->updateIssue('1', [
            'summary' => 'Updated Issue',
            'type' => new IssueType(['id' => 1]),
            'priority' => new IssuePriority(['id' => 1]),
        ]);

        // Then
        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertEquals($updatedIssue['id'], $issue->id);
        $this->assertEquals($updatedIssue['fields']['summary'], $issue->summary);
    }

    #[Test]
    public function it_should_update_an_issue_status()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $transitions = [
            'transitions' => [
                [
                    'id' => '11',
                    'name' => 'To Do',
                    'to' => ['id' => '10001', 'name' => 'To Do'],
                ],
                [
                    'id' => '21',
                    'name' => 'In Progress',
                    'to' => ['id' => '3', 'name' => 'In Progress'],
                ],
            ],
        ];

        $updatedIssue = [
            'id' => '1',
            'key' => 'KEY',
            'fields' => [
                'summary' => 'My Issue',
                'status' => ['id' => '3', 'name' => 'In Progress'],
            ],
        ];

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'GET' && $uri === 'issue/1/transitions';
            })
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode($transitions)));

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'POST' && $uri === 'issue/1/transitions';
            })
            ->once()
            ->andReturn(new Response(204, ['Content-Type' => 'application/json'], null));

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'GET' && $uri === 'issue/1';
            })
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode($updatedIssue)));

        // When
        $issue = $jira->updateIssueStatus('1', new IssueStatus(['id' => '3', 'name' => 'In Progress']));

        // Then
        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertEquals($updatedIssue['id'], $issue->id);
        $this->assertEquals($updatedIssue['key'], $issue->key);
    }

    #[Test]
    public function it_should_throw_a_failed_action_exception_when_no_transition_is_available_for_requested_status()
    {
        // Given
        $jira = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], 'myorg', $this->token);

        $jira->setClient($service = Mockery::mock('\GuzzleHttp\Client'));

        $transitions = [
            'transitions' => [
                [
                    'id' => '11',
                    'name' => 'To Do',
                    'to' => ['id' => '10001', 'name' => 'To Do'],
                ],
                [
                    'id' => '21',
                    'name' => 'In Progress',
                    'to' => ['id' => '3', 'name' => 'In Progress'],
                ],
            ],
        ];

        $service->shouldReceive('request')
            ->withArgs(function ($verb, $uri) {
                return $verb === 'GET' && $uri === 'issue/1/transitions';
            })
            ->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode($transitions)));

        $this->expectException(FailedActionException::class);

        // When
        $jira->updateIssueStatus('1', new IssueStatus(['id' => '999', 'name' => 'Unavailable Status']));
    }
}
