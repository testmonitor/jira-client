<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsUsers;
use TestMonitor\Jira\Responses\PaginatedResponse;

trait ManagesUsers
{
    use TransformsUsers;

    /**
     * Get a list of assignable users for a project.
     *
     * @param string $projectId
     * @param string $query
     * @param int $offset
     * @param int $limit
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Responses\PaginatedResponse
     */
    public function users($projectId, string $query = '', int $offset = 0, int $limit = 50)
    {
        $response = $this->get('user/assignable/search', [
            'query' => [
                'project' => $projectId,
                'query' => $query,
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return new PaginatedResponse(
            $this->fromJiraUsers($response ?? []),
            count($response),
            $limit,
            $offset
        );
    }

    /**
     * Returns the profile of the authenticated user.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\User
     */
    public function myself()
    {
        $response = $this->get("https://api.atlassian.com/ex/jira/{$this->cloudId}/rest/api/3/myself");

        return $this->fromJiraUser($response);
    }
}
