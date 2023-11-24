<?php

namespace TestMonitor\Jira\Resources;

class IssuePriority extends Resource
{
    /**
     * The ID of the issue priority.
     *
     * @var string
     */
    public $id;

    /**
     * The name of the issue priority.
     *
     * @var string
     */
    public $name;

    /**
     * The icon url for the issue priority.
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
        $this->iconUrl = $attributes['iconUrl'] ?? '';
        $this->name = $attributes['name'] ?? '';
    }
}
