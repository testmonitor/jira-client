<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsUsers;

trait ManagesUsers
{
    use TransformsUsers;

    /**
     * Get a list of assignable users for a project.
     *
     * @param string $projectId
     * @param string $query
     * @param int $page
     * @param int $limit
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\User[]
     */
    public function users($projectId, string $query = '', int $page = 1, int $limit = 50)
    {
        $response = $this->get('user/assignable/search', [
            'query' => [
                'project' => $projectId,
                'query' => $query,
                'startAt' => $page,
                'maxResults' => $limit,
            ],
        ]);

        return $this->fromJiraUsers($response);
    }
}
