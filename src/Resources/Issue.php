<?php

namespace TestMonitor\Jira\Resources;

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
     * @param string $summary
     * @param string $description
     * @param string $type
     * @param string $projectKey
     * @param string $id
     * @param string $key
     */
    public function __construct(
        string $summary,
        string $description,
        string $type,
        string $projectKey,
        ?string $id = null,
        ?string $key = null
    ) {
        $this->id = $id;
        $this->key = $key;
        $this->summary = $summary;
        $this->description = $description;
        $this->type = $type;
        $this->projectKey = $projectKey;
    }
}
