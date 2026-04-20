<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsIssuePriorities;

trait ManagesIssuePriorities
{
    use TransformsIssuePriorities;

    /**
     * Get a list of issue priorities for a project.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function issuePriorities(string $projectId): array
    {
        $response = $this->get('priority/search', [
            'query' => ['projectId' => $projectId],
        ]);

        return $this->fromJiraIssuePriorities($response['values']);
    }
}
