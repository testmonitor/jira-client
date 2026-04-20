<?php

namespace TestMonitor\Jira\Resources;

use DH\Adf\Node\Block\Document;
use TestMonitor\Jira\Validator;
use DH\Adf\Exporter\Html\Block\DocumentExporter;

class Issue extends Resource
{
    public ?string $id;

    public ?string $key;

    public string $summary;

    public mixed $description;

    public IssueType|null $type;

    public IssueStatus|null $status;

    public IssuePriority|null $priority;

    public array|null $attachments;

    public Project|null $project;

    /**
     * Create a new resource instance.
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
