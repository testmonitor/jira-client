<?php

namespace TestMonitor\Jira\Resources;

use TestMonitor\Jira\Validator;

class Issue extends Resource
{
    /**
     * The id of the issue.
     *
     * @var string
     */
    public $id;

    /**
     * The key of the issue.
     *
     * @var string
     */
    public $key;

    /**
     * The summary of the issue.
     *
     * @var string
     */
    public $summary;

    /**
     * The description of the issue.
     *
     * @var string
     */
    public $description;

    /**
     * The issue type.
     *
     * @var string
     */
    public $type;

    /**
     * The key of the project.
     *
     * @var string
     */
    public $projectKey;

    /**
     * Create a new resource instance.
     *
     * @param array $issue
     */
    public function __construct(array $issue)
    {
        Validator::keysExists($issue, ['summary', 'description', 'type']);

        $this->id = $issue['id'] ?? null;
        $this->key = $issue['key'] ?? null;
        $this->summary = $issue['summary'];
        $this->description = $issue['description'];
        $this->type = $issue['type'];
        $this->projectKey = $issue['projectKey'] ?? null;
    }
}
