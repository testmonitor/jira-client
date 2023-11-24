<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsProjects;

trait ManagesProjects
{
    use TransformsProjects;

    /**
     * Get a list of projects.
     *
     * @param string $query
     * @param int $page
     * @param int $limit
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Project[]
     */
    public function projects(string $query = '', int $page = 1, int $limit = 50)
    {
        $response = $this->get('project/search', [
            'query' => [
                'query' => $query,
                'startAt' => $page,
                'maxResults' => $limit,
            ],
        ]);

        return $this->fromJiraProjects($response['values'] ?? []);
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
