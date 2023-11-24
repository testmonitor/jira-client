<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsIssueTypes;

trait ManagesIssueTypes
{
    use TransformsIssueTypes;

    /**
     * Get a list of issue types from a project.
     *
     * @param string $projectId
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueType[]
     */
    public function issueTypes(string $projectId)
    {
        $response = $this->get('issuetype/project', [
            'query' => ['projectId' => $projectId],
        ]);

        return $this->fromJiraIssueTypes($response);
    }
}
