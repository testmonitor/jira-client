<?php

namespace TestMonitor\Jira\Resources;

class Account extends Resource
{
    public string $id;

    public string $url;

    public string $name;

    public array $scopes;

    public string $avatarUrl;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->name = $attributes['name'];
        $this->url = $attributes['url'];
        $this->scopes = $attributes['scopes'] ?? [];
        $this->avatarUrl = $attributes['avatarUrl'] ?? '';
    }
}
