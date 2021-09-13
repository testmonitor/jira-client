<?php

namespace TestMonitor\Jira\Actions;

use JiraRestApi\JiraException;
use JiraRestApi\Issue\JqlQuery;
use TestMonitor\Jira\Resources\Issue;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Transforms\TransformsIssues;

trait ManagesIssues
{
    use TransformsIssues;

    /**
     * Get a list of of issues.
     *
     * @param string $projectKey
     * @param int $startAt
     * @param int $maxResults
     *
     * @throws \TestMonitor\Jira\Exceptions\Exception
     *
     * @return Issue[]
     */
    public function issues(string $projectKey, int $startAt = 0, int $maxResults = 15)
    {
        try {
            $jql = (new JqlQuery())->setProject($projectKey);

            $result = $this->issueService()->search($jql->getQuery(), $startAt, $maxResults);

            return array_map(function ($issue) {
                return $this->fromJiraIssue($issue);
            }, $result->issues);
        } catch (JiraException $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Get a single issue.
     *
     * @param string $key
     *
     * @throws \TestMonitor\Jira\Exceptions\Exception
     *
     * @return Issue
     */
    public function issue($key)
    {
        try {
            $issue = $this->issueService()->get($key);

            return $this->fromJiraIssue($issue);
        } catch (JiraException $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Create a new issue.
     *
     * @param \TestMonitor\Jira\Resources\Issue $issue
     * @param string $projectKey
     *
     * @throws \TestMonitor\Jira\Exceptions\Exception
     *
     * @return Issue
     */
    public function createIssue(Issue $issue, string $projectKey)
    {
        try {
            $result = $this->issueService()->create($this->toJiraIssue($issue, $projectKey));

            return $this->issue($result->key);
        } catch (JiraException $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
