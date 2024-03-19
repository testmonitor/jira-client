<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsIssueStatuses;

trait ManagesIssueStatuses
{
    use TransformsIssueStatuses;

    /**
     * Get a list of issue statuses from a project.
     *
     * @param string $projectId
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueStatus[]
     */
    public function issueStatuses(string $projectId)
    {
        $response = $this->get('statuses/search', [
            'query' => ['projectId' => $projectId],
        ]);

        return $this->fromJiraIssueStatuses($response['values']);
    }

    /**
     * Get a list of issue statuses for an issue type.
     *
     * @param string $projectId
     * @param string $typeId
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueStatus[]
     */
    public function issueStatusesForType(string $projectId, string $typeId)
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
