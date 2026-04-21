<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\IssueType;

trait TransformsIssueTypes
{
    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\IssueType>
     */
    protected function fromJiraIssueTypes(mixed $issueTypes): array
    {
        Validator::isArray($issueTypes);

        return array_map(function ($issueType) {
            return $this->fromJiraIssueType($issueType);
        }, $issueTypes);
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
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
