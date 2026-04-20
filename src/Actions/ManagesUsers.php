<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\User;
use TestMonitor\Jira\Transforms\TransformsUsers;
use TestMonitor\Jira\Responses\LengthAwarePaginatedResponse;

trait ManagesUsers
{
    use TransformsUsers;

    /**
     * Get a list of assignable users for a project.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function users(
        string $projectId,
        string $query = '',
        int $offset = 0,
        int $limit = 50
    ): LengthAwarePaginatedResponse {
        $response = $this->get('user/assignable/search', [
            'query' => [
                'project' => $projectId,
                'query' => $query,
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return new LengthAwarePaginatedResponse(
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
     */
    public function myself(): User
    {
        $response = $this->get("https://api.atlassian.com/ex/jira/{$this->cloudId}/rest/api/3/myself");

        return $this->fromJiraUser($response);
    }
}
