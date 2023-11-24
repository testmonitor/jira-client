<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsProjectVersions;

trait ManagesProjectVersions
{
    use TransformsProjectVersions;

    /**
     * Get a list of projects versions.
     *
     * @param string $projectId
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\ProjectVersion[]
     */
    public function projectVersions($projectId)
    {
        $response = $this->get("project/{$projectId}/version");

        return $this->fromJiraProjectVersions($response['values'] ?? []);
    }
}
