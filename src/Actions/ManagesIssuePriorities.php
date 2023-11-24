<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsIssuePriorities;

trait ManagesIssuePriorities
{
    use TransformsIssuePriorities;

    /**
     * Get a list of issue priorities from a project.
     *
     * @param string $projectId
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\IssuePriority[]
     */
    public function issuePriorities(string $projectId)
    {
        $response = $this->get('priority/search', [
            'query' => ['projectId' => $projectId],
        ]);

        return $this->fromJiraIssuePriorities($response['values']);
    }
}
