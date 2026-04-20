<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsIssueTypes;

trait ManagesIssueTypes
{
    use TransformsIssueTypes;

    /**
     * Get a list of issue types for a project.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function issueTypes(string $projectId): array
    {
        $response = $this->get('issuetype/project', [
            'query' => ['projectId' => $projectId],
        ]);

        return $this->fromJiraIssueTypes($response);
    }
}
