<?php

namespace TestMonitor\Jira\Transforms;

use JiraRestApi\Issue\IssueField;
use TestMonitor\Jira\Resources\Issue;

trait TransformsIssues
{
    /**
     * @param \TestMonitor\Jira\Resources\Issue $issue
     * @param string|null $projectKey
     * @return \JiraRestApi\Issue\IssueField
     */
    protected function toJiraIssue(Issue $issue, string $projectKey = null): IssueField
    {
        $issueField = new IssueField();

        return $issueField
            ->setProjectKey($projectKey ?? $issue->projectKey)
            ->setSummary($issue->summary)
            ->setIssueType($issue->type)
            ->setDescription($issue->description);
    }

    /**
     * @param \JiraRestApi\Issue\Issue $issue
     * @return \TestMonitor\Jira\Resources\Issue
     */
    protected function fromJiraIssue(\JiraRestApi\Issue\Issue $issue): Issue
    {
        return new Issue([
            'summary' => $issue->fields->summary ?? '',
            'description' => $issue->fields->description ?? '',
            'type' => $issue->fields->getIssueType()->name,
            'projectKey' => $issue->fields->getProjectKey(),
            'id' => $issue->id,
            'key' => $issue->key,
        ]);
    }
}
