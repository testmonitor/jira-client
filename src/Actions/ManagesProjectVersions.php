<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\ProjectVersion;
use TestMonitor\Jira\Transforms\TransformsProjectVersions;

trait ManagesProjectVersions
{
    use TransformsProjectVersions;

    /**
     * Get a list of project versions.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function projectVersions(
        string $projectId,
        string $query = '',
        int $offset = 0,
        int $limit = 50,
        string $orderBy = ProjectVersion::ORDER_SEQUENCE_DESC,
    ): array {
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
