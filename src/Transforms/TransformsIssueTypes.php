<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\IssueType;

trait TransformsIssueTypes
{
    /**
     * @param array $issueTypes
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueType[]
     */
    protected function fromJiraIssueTypes($issueTypes): array
    {
        Validator::isArray($issueTypes);

        return array_map(function ($issueType) {
            return $this->fromJiraIssueType($issueType);
        }, $issueTypes);
    }

    /**
     * @param array $issueType
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssueType
     */
    protected function fromJiraIssueType(array $issueType): IssueType
    {
        Validator::keysExists($issueType, ['id']);

        return new IssueType([
            'id' => $issueType['id'],
            'name' => $issueType['name'] ?? '',
            'description' => $issueType['description'] ?? '',
            'iconUrl' => $issueType['iconUrl'] ?? '',
        ]);
    }
}
