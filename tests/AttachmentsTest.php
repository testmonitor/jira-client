<?php

namespace TestMonitor\Jira\Tests;

use JiraRestApi\JiraException;
use Mockery;
use TestMonitor\Jira\Client;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Resources\Attachment;

class AttachmentsTest extends TestCase
{
    protected $attachment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attachment = Mockery::mock('\JiraRestApi\Issue\Attachment');

        $this->attachment->id = '1';
        $this->attachment->filename = 'file.jpg';
        $this->attachment->content = 'https://jira.atlassian.net/TST/secure/1';
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_add_an_attachment_to_an_issue()
    {
        // Given
        $jira = new Client('url', 'user', 'pass');

        $jira->setIssueService($service = Mockery::mock('JiraRestApi\Issue\IssueService'));

        $service->shouldReceive('addAttachments')->once()->andReturn([$this->attachment]);

        // When
        $attachment = $jira->addAttachment('TST', 'file.jpg');

        // Then
        $this->assertInstanceOf(Attachment::class, $attachment);
        $this->assertEquals($this->attachment->id, $attachment->id);
        $this->assertEquals($this->attachment->filename, $attachment->filename);
    }

    /** @test */
    public function it_should_throw_an_exception_when_client_fails_to_add_an_attachment()
    {
        // Given
        $jira = new Client('url', 'user', 'pass');

        $jira->setIssueService($service = Mockery::mock('JiraRestApi\Issue\IssueService'));

        $service->shouldReceive('addAttachments')->once()->andThrow(new JiraException());

        $this->expectException(Exception::class);

        // When
        $jira->addAttachment('Attach', 'Me');
    }
}
