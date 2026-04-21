<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Account;

trait TransformsAccounts
{
    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\Account>
     */
    protected function fromJiraAccounts(mixed $accounts): array
    {
        Validator::isArray($accounts);

        return array_map(function ($account) {
            return $this->fromJiraAccount($account);
        }, $accounts);
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraAccount(array $account): Account
    {
        Validator::keysExists($account, ['id', 'url']);

        return new Account([
            'id' => $account['id'],
            'url' => $account['url'],
            'name' => $account['name'],
            'scopes' => $account['scopes'],
            'avatarUrl' => $account['avatarUrl'],
        ]);
    }
}
