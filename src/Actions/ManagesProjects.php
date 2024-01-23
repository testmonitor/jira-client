<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Responses\PaginatedResponse;
use TestMonitor\Jira\Transforms\TransformsProjects;

trait ManagesProjects
{
    use TransformsProjects;

    /**
     * Get a list of projects.
     *
     * @param string $query
     * @param int $offset
     * @param int $limit
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Responses\PaginatedResponse
     */
    public function projects(string $query = '', int $offset = 0, int $limit = 50)
    {
        $response = $this->get('project/search', [
            'query' => [
                'query' => $query,
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return new PaginatedResponse(
            $this->fromJiraProjects($response['values'] ?? []),
            $response['total'],
            $response['maxResults'],
            $response['startAt']
        );
    }

    /**
     * Get a single project.
     *
     * @param string $id
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Issue
     */
    public function project($id)
    {
        $response = $this->get("project/{$id}");

        return $this->fromJiraProject($response);
    }
}
