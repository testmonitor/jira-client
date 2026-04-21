<?php

namespace TestMonitor\Jira\Resources;

class IssueStatus extends Resource
{
    public string $id;

    public string $name;

    public string $description;

    public string $iconUrl;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes)
    {
        $this->id = (string) $attributes['id'];
        $this->name = $attributes['name'] ?? '';
        $this->description = $attributes['description'] ?? '';
        $this->iconUrl = $attributes['iconUrl'] ?? '';
    }
}
