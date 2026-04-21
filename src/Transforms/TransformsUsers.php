<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\User;

trait TransformsUsers
{
    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\User>
     */
    protected function fromJiraUsers(mixed $users): array
    {
        Validator::isArray($users);

        return array_map(function ($user) {
            return $this->fromJiraUser($user);
        }, $users);
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraUser(array $user): User
    {
        Validator::keysExists($user, ['accountId', 'emailAddress']);

        return new User([
            'id' => $user['accountId'],
            'type' => $user['accountType'] ?? '',
            'emailAddress' => $user['emailAddress'],
            'displayName' => $user['displayName'] ?? '',
            'timezone' => $user['timeZone'] ?? '',
        ]);
    }
}
