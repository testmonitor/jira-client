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
}
