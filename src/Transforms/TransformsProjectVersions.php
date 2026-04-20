<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\ProjectVersion;

trait TransformsProjectVersions
{
    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraProjectVersions(mixed $projectVersions): array
    {
        Validator::isArray($projectVersions);

        return array_map(function ($projectVersion) {
            return $this->fromJiraProjectVersion($projectVersion);
        }, $projectVersions);
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    protected function fromJiraProjectVersion(array $projectVersion): ProjectVersion
    {
        Validator::keysExists($projectVersion, ['id']);

        return new ProjectVersion([
            'id' => $projectVersion['id'],
            'name' => $projectVersion['name'] ?? null,
            'description' => $projectVersion['description'] ?? null,
            'archived' => $projectVersion['archived'] ?? null,
            'released' => $projectVersion['released'] ?? null,
        ]);
    }
}
