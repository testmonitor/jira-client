<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsIssueStatuses;

trait ManagesIssueStatuses
{
    use TransformsIssueStatuses;

    /**
     * Get a list of issue statuses for a project.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function issueStatuses(string $projectId): array
    {
        $response = $this->get('statuses/search', [
            'query' => ['projectId' => $projectId],
        ]);

        return $this->fromJiraIssueStatuses($response['values']);
    }

    /**
     * Get a list of issue statuses for an issue type.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function issueStatusesForType(string $projectId, string $typeId): array
    {
        $response = $this->get("project/{$projectId}/statuses");

        $filtered = array_merge(
            ...array_column(
                array_filter(
                    $response,
                    fn ($type) => $type['id'] === $typeId
                ),
                'statuses'
            )
        );

        return $this->fromJiraIssueStatuses($filtered);
    }
}
