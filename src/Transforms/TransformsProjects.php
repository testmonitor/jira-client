<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Project;

trait TransformsProjects
{
    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraProjects(mixed $projects): array
    {
        Validator::isArray($projects);

        return array_map(function ($project) {
            return $this->fromJiraProject($project);
        }, $projects);
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraProject(array $project): Project
    {
        Validator::keysExists($project, ['id', 'key']);

        return new Project([
            'id' => $project['id'] ?? '',
            'key' => $project['key'] ?? '',
            'name' => $project['name'] ?? '',
        ]);
    }
}
