<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Parsers\Adf\Document;
use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Issue;
use TestMonitor\Jira\Resources\Project;
use TestMonitor\Jira\Resources\IssueType;
use TestMonitor\Jira\Resources\IssueStatus;
use TestMonitor\Jira\Resources\IssuePriority;

trait TransformsIssues
{
    use TransformsAttachments;

    /**
     * @param \TestMonitor\Jira\Resources\Issue $issue
     * @return array
     */
    protected function toUpdateIssue(array $attributes): array
    {
        return [
            'fields' => array_filter([
                'summary' => $attributes['summary'] ?? null,
                'description' => $attributes['description'] ?? null,
                'issuetype' => $attributes['type'] instanceof IssueType ?
                    ['id' => $attributes['type']->id] : null,
                'priority' => $attributes['priority'] instanceof IssuePriority ?
                    ['id' => $attributes['priority']->id] : null,
            ]),
        ];
    }

    /**
     * @param \TestMonitor\Jira\Resources\Issue $issue
     * @return array
     */
    protected function toNewIssue(Issue $issue): array
    {
        return [
            'fields' => array_filter([
                'summary' => $issue->summary,
                'description' => $issue->description,
                'project' => ['id' => $issue->project->id],
                'issuetype' => ['id' => $issue->type->id],
                'priority' => $issue->priority instanceof IssuePriority ? ['id' => $issue->priority->id] : null,
            ]),
        ];
    }

    /**
     * @param array $issues
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Issue[]
     */
    protected function fromJiraIssues($issues): array
    {
        Validator::isArray($issues);

        return array_map(function ($issue) {
            return $this->fromJiraIssue($issue);
        }, $issues);
    }

    /**
     * @param array $issue
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Issue
     */
    protected function fromJiraIssue(array $issue): Issue
    {
        Validator::keysExists($issue, ['id', 'key']);

        return new Issue([
            'id' => $issue['id'] ?? '',
            'key' => $issue['key'] ?? '',
            'self' => $issue['self'] ?? '',
            'summary' => $issue['fields']['summary'] ?? '',

            'description' => isset($issue['fields']['description']) ?
                (new Document($issue['fields']['description']))->toBlockNode() : null,

            'type' => isset($issue['fields']['issuetype']) ?
                new IssueType($issue['fields']['issuetype']) : null,
            'status' => isset($issue['fields']['status']) ?
                new IssueStatus($issue['fields']['status']) : null,
            'priority' => isset($issue['fields']['priority']) ?
                new IssuePriority($issue['fields']['priority']) : null,

            'attachments' => isset($issue['fields']['attachment']) ?
                $this->fromJiraAttachments($issue['fields']['attachment']) : [],

            'project' => isset($issue['fields']['project']) ?
                new Project($issue['fields']['project']) : null,
        ]);
    }
}
