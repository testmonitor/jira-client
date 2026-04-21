<?php

namespace TestMonitor\Jira\Resources;

class Project extends Resource
{
    public ?string $id;

    public ?string $key;

    public ?string $name;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'] ?? null;
        $this->key = $attributes['key'] ?? null;
        $this->name = $attributes['name'] ?? null;
    }
}
