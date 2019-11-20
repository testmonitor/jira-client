<?php

namespace TestMonitor\Jira\Actions;

use JiraRestApi\JiraException;
use TestMonitor\Jira\Resources\Project;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Transforms\TransformsProjects;

trait ManagesProjects
{
    use TransformsProjects;

    /**
     * Get a list of of projects.
     *
     * @throws \TestMonitor\Jira\Exceptions\Exception
     * @return Project[]
     */
    public function projects()
    {
        try {
            $projects = $this->projectService()->getAllProjects();

            return array_map(function ($project) {
                return $this->fromJiraProject($project);
            }, $projects->getArrayCopy());
        } catch (JiraException $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Get a single project.
     *
     * @param string $key
     *
     * @throws \TestMonitor\Jira\Exceptions\Exception
     * @return Project
     */
    public function project($key)
    {
        try {
            $project = $this->projectService()->get($key);

            return $this->fromJiraProject($project);
        } catch (JiraException $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
