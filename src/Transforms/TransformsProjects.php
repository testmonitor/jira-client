<?php

namespace TestMonitor\Jira\Transforms;

use JiraRestApi\Issue\IssueType;
use TestMonitor\Jira\Resources\Project;
use JiraRestApi\Project\Project as JiraProject;

trait TransformsProjects
{
    /**
     * @param JiraProject $project
     * @return \TestMonitor\Jira\Resources\Project
     */
    protected function fromJiraProject(\JiraRestApi\Project\Project $project): Project
    {
        return new Project([
            'id' => $project->id,
            'key' => $project->key,
            'name' => $project->name,
            'issueTypes' => array_map(function (IssueType $issueType) {
                return $issueType->name;
            }, $project->issueTypes ?? []),
        ]);
    }
}
