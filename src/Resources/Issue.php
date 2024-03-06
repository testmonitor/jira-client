<?php

namespace TestMonitor\Jira\Resources;

use DH\Adf\Node\Block\Document;
use TestMonitor\Jira\Validator;
use DH\Adf\Exporter\Html\Block\DocumentExporter;

class Issue extends Resource
{
    /**
     * The ID of the issue.
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
     * @var \DH\Adf\Node\Block\Document|null
     */
    public $description;

    /**
     * The issue type.
     *
     * @var \TestMonitor\Jira\Resources\IssueType
     */
    public $type;

    /**
     * The issue status.
     *
     * @var \TestMonitor\Jira\Resources\IssueStatus
     */
    public $status;

    /**
     * The issue priority.
     *
     * @var \TestMonitor\Jira\Resources\IssuePriority
     */
    public $priority;

    /**
     * The list of attachments.
     *
     * @var array
     */
    public $attachments;

    /**
     * The project of the issue.
     *
     * @var \TestMonitor\Jira\Resources\Project
     */
    public $project;

    /**
     * Create a new resource instance.
     *
     * @param array $issue
     */
    public function __construct(array $issue)
    {
        Validator::keysExists($issue, ['summary', 'description']);

        $this->id = $issue['id'] ?? null;
        $this->key = $issue['key'] ?? null;
        $this->summary = $issue['summary'];

        $this->description = $issue['description'];

        $this->type = $issue['type'] ?? null;
        $this->status = $issue['status'] ?? null;
        $this->priority = $issue['priority'] ?? null;

        $this->project = $issue['project'] ?? null;
        $this->attachments = $issue['attachments'] ?? null;
    }

    /**
     * Returns the description field as HTML.
     *
     * @return string
     */
    public function getDescriptionAsHTML(): string
    {
        if (! $this->description instanceof Document) {
            return (string) $this->description;
        }

        $document = new DocumentExporter($this->description);

        return $document->export();
    }
}
