<?php

namespace TestMonitor\Jira\Resources;

class IssueType extends Resource
{
    /**
     * The ID of the issue type.
     *
     * @var string
     */
    public $id;

    /**
     * The name of the issue type.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the issue type.
     *
     * @var string
     */
    public $description;

    /**
     * The icon url for the issue type.
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
