<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\ProjectVersion;
use TestMonitor\Jira\Transforms\TransformsProjectVersions;

trait ManagesProjectVersions
{
    use TransformsProjectVersions;

    /**
     * Get a list of projects versions.
     *
     * @param string $projectId
     * @param string $query
     * @param int $offset
     * @param int $limit
     * @param string $orderBy
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\ProjectVersion[]
     */
    public function projectVersions(
        $projectId,
        string $query = '',
        int $offset = 0,
        int $limit = 50,
        string $orderBy = ProjectVersion::ORDER_SEQUENCE_DESC,
    ) {
        $response = $this->get("project/{$projectId}/version", [
            'query' => [
                'query' => $query,
                'startAt' => $offset,
                'maxResults' => $limit,
                'orderBy' => $orderBy,
            ],
        ]);

        return $this->fromJiraProjectVersions($response['values'] ?? []);
    }
}
