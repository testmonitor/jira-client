<?php

namespace TestMonitor\Jira\Actions;

use JqlBuilder\Jql;
use TestMonitor\Jira\Resources\Issue;
use TestMonitor\Jira\Resources\IssueStatus;
use TestMonitor\Jira\Responses\PaginatedResponse;
use TestMonitor\Jira\Transforms\TransformsIssues;
use TestMonitor\Jira\Exceptions\FailedActionException;

trait ManagesIssues
{
    use TransformsIssues;

    /**
     * Get a single issue.
     *
     * @param string $id
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Issue
     */
    public function issue($id)
    {
        $response = $this->get("issue/{$id}");

        return $this->fromJiraIssue($response);
    }

    /**
     * Get a list of issues.
     *
     * @param \JqlBuilder\Jql|null $query
     * @param int $offset
     * @param int $limit
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Responses\PaginatedResponse
     */
    public function issues(Jql $query = null, int $offset = 0, int $limit = 50)
    {
        $response = $this->get('search', [
            'query' => [
                'jql' => $query instanceof Jql ? $query->getQuery() : '',
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return new PaginatedResponse(
            $this->fromJiraIssues($response['issues'] ?? []),
            $response['total'],
            $response['maxResults'],
            $response['startAt']
        );
    }

    /**
     * Create a new issue.
     *
     * @param \TestMonitor\Jira\Resources\Issue $issue
     * @return \TestMonitor\Jira\Resources\Issue
     */
    public function createIssue(Issue $issue): Issue
    {
        $response = $this->post('issue', ['json' => $this->toNewIssue($issue)]);

        return $this->fromJiraIssue($response);
    }

    /**
     * Update an issue.
     *
     * @param string $id
     * @param array{
     *      summary: string,
     *      description: \DH\Adf\Node\Block\Document,
     *      type: \TestMonitor\Jira\Resources\IssueType,
     *      priority: \TestMonitor\Jira\Resources\IssuePriority
     *  } $attributes
     * @return \TestMonitor\Jira\Resources\Issue
     */
    public function updateIssue($id, array $attributes): Issue
    {
        $this->put("issue/{$id}", ['json' => $this->toUpdateIssue($attributes)]);

        return $this->issue($id);
    }

    /**
     * Update the status of an issue.
     *
     * @param \TestMonitor\Jira\Resources\Issue $issue
     *
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     *
     * @return \TestMonitor\Jira\Resources\Issue
     */
    public function updateIssueStatus($issueId, IssueStatus $status): Issue
    {
        $transition = $this->findTransitionForStatus($issueId, $status);

        if (empty($transition)) {
            throw new FailedActionException(json_encode([
                'errorMessages' => ['Unable to transition this issue to requested status.'],
            ]), 400);
        }

        $this->post("issue/{$issueId}/transitions", [
            'json' => [
                'transition' => ['id' => $transition['id']],
            ],
        ]);

        return $this->issue($issueId);
    }

    /**
     * Determine the transition for a given issue and status.
     *
     * @param string $issueId
     * @param \TestMonitor\Jira\Resources\IssueStatus $status
     * @return null|array
     */
    protected function findTransitionForStatus($issueId, IssueStatus $status): ?array
    {
        // Get the available transitions for this issue.
        $response = $this->get("issue/{$issueId}/transitions");

        // Find matching transitions for the given status.
        $transitions = array_filter(
            $response['transitions'],
            fn (array $transition) => $transition['to']['id'] === $status->id
        );

        // Return the first matching transition
        return array_shift($transitions);
    }
}
