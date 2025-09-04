<?php

namespace TestMonitor\Jira\Actions;

use JqlBuilder\Jql;
use TestMonitor\Jira\Resources\Issue;
use TestMonitor\Jira\Resources\IssueStatus;
use TestMonitor\Jira\Transforms\TransformsIssues;
use TestMonitor\Jira\Exceptions\FailedActionException;
use TestMonitor\Jira\Responses\TokenPaginatedResponse;

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
     * @param int $limit
     * @param string $nextPageToken
     * @param array $fields
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Responses\TokenPaginatedResponse
     */
    public function issues(
        ?Jql $query = null,
        int $limit = 50,
        ?string $nextPageToken = null,
        array $fields = ['*navigable']
    ) {

        $response = $this->get('search/jql', [
            'query' => [
                'jql' => $query instanceof Jql ? $query->getQuery() : '',
                'maxResults' => $limit,
                'fields' => implode(',', $fields),
                'nextPageToken' => $nextPageToken,
            ],
        ]);

        return new TokenPaginatedResponse(
            $this->fromJiraIssues($response['issues'] ?? []),
            $this->countIssues($query),
            $limit,
            $response['nextPageToken'] ?? '',
            $response['isLast'] ?? false
        );
    }

    /**
     * Count the number of Jira issues.
     *
     * @param \JqlBuilder\Jql|null $query
     * @return int
     */
    public function countIssues(?Jql $query = null)
    {
        $response = $this->post('search/approximate-count', [
            'json' => [
                'jql' => $query instanceof Jql ? $query->getQuery() : '',
            ],
        ]);

        return $response['count'];
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
            throw new FailedActionException('Unable to transition this issue to requested status');
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
