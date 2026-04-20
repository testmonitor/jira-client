<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\Project;
use TestMonitor\Jira\Transforms\TransformsProjects;
use TestMonitor\Jira\Responses\LengthAwarePaginatedResponse;

trait ManagesProjects
{
    use TransformsProjects;

    /**
     * Get a list of projects.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function projects(string $query = '', int $offset = 0, int $limit = 50): LengthAwarePaginatedResponse
    {
        $response = $this->get('project/search', [
            'query' => [
                'query' => $query,
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return new LengthAwarePaginatedResponse(
            $this->fromJiraProjects($response['values'] ?? []),
            $response['total'],
            $response['maxResults'],
            $response['startAt']
        );
    }

    /**
     * Get a single project.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function project(string $id): Project
    {
        $response = $this->get("project/{$id}");

        return $this->fromJiraProject($response);
    }
}
