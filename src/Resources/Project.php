<?php

namespace TestMonitor\Jira\Resources;

class Project extends Resource
{
    /**
     * The ID of the project.
     *
     * @var string
     */
    public $id;

    /**
     * The key of the project.
     *
     * @var string
     */
    public $key;

    /**
     * The name of the project.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new resource instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'] ?? null;
        $this->key = $attributes['key'] ?? null;
        $this->name = $attributes['name'] ?? null;
    }
}
