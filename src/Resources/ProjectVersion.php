<?php

namespace TestMonitor\Jira\Resources;

class ProjectVersion extends Resource
{
    /**
     * Project version order fields.
     */
    public const string ORDER_RELEASE_DATE_ASC = '+releaseDate';
    public const string ORDER_RELEASE_DATE_DESC = '-releaseDate';
    public const string ORDER_SEQUENCE_ASC = '+sequence';
    public const string ORDER_SEQUENCE_DESC = '-sequence';

    public ?string $id;

    public ?string $name;

    public ?string $description;

    public ?bool $archived;

    public ?bool $released;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'] ?? null;
        $this->name = $attributes['name'] ?? null;
        $this->description = $attributes['description'] ?? null;
        $this->archived = $attributes['archived'] ?? null;
        $this->released = $attributes['released'] ?? null;
    }
}
