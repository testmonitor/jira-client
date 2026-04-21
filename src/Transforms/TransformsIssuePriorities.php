<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\IssuePriority;

trait TransformsIssuePriorities
{
    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\IssuePriority>
     */
    protected function fromJiraIssuePriorities(mixed $issuePriorities): array
    {
        Validator::isArray($issuePriorities);

        return array_map(function ($issuePriority) {
            return $this->fromJiraIssuePriority($issuePriority);
        }, $issuePriorities);
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraIssuePriority(array $issuePriority): IssuePriority
    {
        Validator::keysExists($issuePriority, ['id']);

        return new IssuePriority([
            'id' => $issuePriority['id'],
            'name' => $issuePriority['name'] ?? '',
            'iconUrl' => $issuePriority['iconUrl'] ?? '',
        ]);
    }
}
