<?php

namespace TestMonitor\Jira\Tests;

use Mockery;
use ArrayObject;
use TestMonitor\Jira\Client;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Resources\Project;

class ProjectsTest extends TestCase
{
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Mockery::mock('\JiraRestApi\Project\Project');
        $this->project->id = '1';
        $this->project->key = 'TST';
        $this->project->name = 'A test';
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_a_list_of_projects()
    {
        // Given
        $jira = new Client('url', 'user', 'pass');

        $jira->setProjectService($service = Mockery::mock('JiraRestApi\Project\ProjectService'));

        $service->shouldReceive('getAllProjects')->once()->andReturn(
            new ArrayObject([$this->project])
        );

        // When
        $projects = $jira->projects();

        // Then
        $this->assertIsArray($projects);
        $this->assertCount(1, $projects);
        $this->assertInstanceOf(Project::class, $projects[0]);
        $this->assertEquals($projects[0]->id, $this->project->id);
    }

    /** @test */
    public function it_should_return_a_single_project()
    {
        // Given
        $jira = new Client('url', 'user', 'pass');

        $jira->setProjectService($service = Mockery::mock('JiraRestApi\Project\ProjectService'));

        $service->shouldReceive('get')->with($this->project->key)->once()->andReturn($this->project);

        // When
        $project = $jira->project($this->project->key);

        // Then
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($this->project->id, $project->id);
    }
}
