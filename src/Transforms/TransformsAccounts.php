<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Account;

trait TransformsAccounts
{
    /**
     * @param array $account
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Account
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
