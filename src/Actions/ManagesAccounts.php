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
     * @return \TestMonitor\Jira\Resources\User
     */
    public function accounts()
    {
        $response = $this->get('https://api.atlassian.com/oauth/token/accessible-resources');

        Validator::isArray($response);

        return $this->fromJiraAccounts($response);
    }

    /**
     * Get the cloud ID for the specified URL.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return string
     */
    public function cloudId(string $instanceUrl): string
    {
        $response = $this->get("{$instanceUrl}/_edge/tenant_info");

        Validator::isArray($response);

        return $response['cloudId'];
    }
}
