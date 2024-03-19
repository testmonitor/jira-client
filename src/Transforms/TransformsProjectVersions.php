<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\ProjectVersion;

trait TransformsProjectVersions
{
    /**
     * @param array $projects
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\ProjectVersion[]
     */
    protected function fromJiraProjectVersions($projectVersions): array
    {
        Validator::isArray($projectVersions);

        return array_map(function ($projectVersion) {
            return $this->fromJiraProjectVersion($projectVersion);
        }, $projectVersions);
    }

    /**
     * @param array $projectVersion
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\ProjectVersion
     */
    protected function fromJiraProjectVersion(array $projectVersion): ProjectVersion
    {
        Validator::keysExists($projectVersion, ['id']);

        return new ProjectVersion([
            'id' => $projectVersion['id'],
            'name' => $projectVersion['name'] ?? null,
            'archived' => $projectVersion['archived'] ?? null,
            'released' => $projectVersion['released'] ?? null,
        ]);
    }
}
