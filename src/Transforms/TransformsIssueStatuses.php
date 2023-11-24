<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\IssueStatus;

trait TransformsIssueStatuses
{
    /**
     * @param array $issueStatuses
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueStatus[]
     */
    protected function fromJiraIssueStatuses($issueStatuses): array
    {
        Validator::isArray($issueStatuses);

        return array_map(function ($issueStatus) {
            return $this->fromJiraIssueStatus($issueStatus);
        }, $issueStatuses);
    }

    /**
     * @param array $issueStatus
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueStatus
     */
    protected function fromJiraIssueStatus(array $issueStatus): IssueStatus
    {
        Validator::keysExists($issueStatus, ['id']);

        return new IssueStatus([
            'id' => $issueStatus['id'],
            'name' => $issueStatus['name'] ?? '',
            'description' => $issueStatus['description'] ?? '',
            'iconUrl' => $issueStatus['iconUrl'] ?? '',
        ]);
    }
}
