<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Transforms\TransformsAccounts;

trait ManagesAccounts
{
    use TransformsAccounts;

    /**
     * Get the current user.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\User
     */
    public function account()
    {
        $response = $this->get('https://api.atlassian.com/oauth/token/accessible-resources');

        Validator::isArray($response);

        return $this->fromJiraAccount($response[0]);
    }
}
