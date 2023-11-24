<?php

namespace TestMonitor\Jira\Resources;

class IssueStatus extends Resource
{
    /**
     * The ID of the issue status.
     *
     * @var string
     */
    public $id;

    /**
     * The name of the issue status.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the issue status.
     *
     * @var string
     */
    public $description;

    /**
     * The icon url for the issue status.
     *
     * @var string
     */
    public $iconUrl;

    /**
     * Create a new resource instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->id = (string) $attributes['id'];
        $this->name = $attributes['name'] ?? '';
        $this->description = $attributes['description'] ?? '';
        $this->iconUrl = $attributes['iconUrl'] ?? '';
    }
}
