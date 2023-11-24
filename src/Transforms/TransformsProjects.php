<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Project;

trait TransformsProjects
{
    /**
     * @param array $projects
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Project[]
     */
    protected function fromJiraProjects($projects): array
    {
        Validator::isArray($projects);

        return array_map(function ($project) {
            return $this->fromJiraProject($project);
        }, $projects);
    }

    /**
     * @param array $project
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Project
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
