<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\Issue;
use TestMonitor\Jira\Builders\JQL\JQL;
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
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function issue(string $id): Issue
    {
        $response = $this->get("issue/{$id}");

        return $this->fromJiraIssue($response);
    }

    /**
     * Get a list of issues.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public function issues(
        ?JQL $query = null,
        int $limit = 50,
        ?string $nextPageToken = null,
        array $fields = ['*navigable']
    ): TokenPaginatedResponse {
        $response = $this->get('search/jql', [
            'query' => [
                'jql' => $query instanceof JQL ? $query->getQuery() : '',
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
     */
    public function countIssues(?JQL $query = null): int
    {
        $response = $this->post('search/approximate-count', [
            'json' => [
                'jql' => $query instanceof JQL ? $query->getQuery() : '',
            ],
        ]);

        return $response['count'];
    }

    /**
     * Create a new issue.
     */
    public function createIssue(Issue $issue): Issue
    {
        $response = $this->post('issue', ['json' => $this->toNewIssue($issue)]);

        return $this->fromJiraIssue($response);
    }

    /**
     * Update an issue.
     */
    public function updateIssue(string $id, array $attributes): Issue
    {
        $this->put("issue/{$id}", ['json' => $this->toUpdateIssue($attributes)]);

        return $this->issue($id);
    }

    /**
     * Update the status of an issue.
     *
     * @throws \TestMonitor\Jira\Exceptions\FailedActionException
     */
    public function updateIssueStatus(string $issueId, IssueStatus $status): Issue
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

    protected function findTransitionForStatus(string $issueId, IssueStatus $status): ?array
    {
        // Get the available transitions for this issue.
        $response = $this->get("issue/{$issueId}/transitions");

        // Find the first matching transition for the given status.
        return array_find(
            $response['transitions'],
            fn (array $transition) => $transition['to']['id'] === $status->id
        );
    }
}
