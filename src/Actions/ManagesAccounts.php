<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Transforms\TransformsAccounts;

trait ManagesAccounts
{
    use TransformsAccounts;

    /**
     * Get a list of atlassian cloud accounts associated to the user.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Account[]
     */
    public function accounts()
    {
        $response = $this->get('https://api.atlassian.com/oauth/token/accessible-resources');

        Validator::isArray($response);

        return $this->fromJiraAccounts($response);
    }
}
