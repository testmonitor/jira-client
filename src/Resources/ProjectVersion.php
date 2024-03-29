<?php

namespace TestMonitor\Jira\Resources;

class ProjectVersion extends Resource
{
    /**
     * Project version order fields.
     */
    public const ORDER_RELEASE_DATE_ASC = '+releaseDate';
    public const ORDER_RELEASE_DATE_DESC = '-releaseDate';
    public const ORDER_SEQUENCE_ASC = '+sequence';
    public const ORDER_SEQUENCE_DESC = '-sequence';

    /**
     * The ID of the project version.
     *
     * @var string
     */
    public $id;

    /**
     * The name of the project version.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the project version.
     *
     * @var string
     */
    public $description;

    /**
     * The archived flag of the project version.
     *
     * @var bool
     */
    public $archived;

    /**
     * The release flag of the project version.
     *
     * @var bool
     */
    public $released;

    /**
     * Create a new resource instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'] ?? null;
        $this->name = $attributes['name'] ?? null;
        $this->archived = $attributes['archived'] ?? null;
        $this->released = $attributes['released'] ?? null;
    }
}
