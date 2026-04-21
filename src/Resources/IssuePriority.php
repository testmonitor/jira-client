<?php

namespace TestMonitor\Jira\Resources;

class IssuePriority extends Resource
{
    public string $id;

    public string $name;

    public string $iconUrl;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes)
    {
        $this->id = (string) $attributes['id'];
        $this->iconUrl = $attributes['iconUrl'] ?? '';
        $this->name = $attributes['name'] ?? '';
    }
}
