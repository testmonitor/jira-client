<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\IssuePriority;

trait TransformsIssuePriorities
{
    /**
     * @param array $issuePriorities
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssuePriority[]
     */
    protected function fromJiraIssuePriorities($issuePriorities): array
    {
        Validator::isArray($issuePriorities);

        return array_map(function ($issuePriority) {
            return $this->fromJiraIssuePriority($issuePriority);
        }, $issuePriorities);
    }

    /**
     * @param array $issuePriority
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssuePriority
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
