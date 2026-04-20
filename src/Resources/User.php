<?php

namespace TestMonitor\Jira\Resources;

class User extends Resource
{
    public string $id;

    public string $type;

    public string $name;

    public string $emailAddress;

    public string $timezone;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->name = $attributes['displayName'];
        $this->emailAddress = $attributes['emailAddress'];
        $this->type = $attributes['type'];
        $this->timezone = $attributes['timezone'];
    }
}
