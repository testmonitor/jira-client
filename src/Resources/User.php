<?php

namespace TestMonitor\Jira\Resources;

class User extends Resource
{
    /**
     * The ID of the user.
     *
     * @var string
     */
    public $id;

    /**
     * The account type of the user.
     *
     * @var string
     */
    public $type;

    /**
     * The name of the user.
     *
     * @var string
     */
    public $name;

    /**
     * The email of the user.
     *
     * @var string
     */
    public $emailAddress;

    /**
     * The timezone for the user.
     *
     * @var string
     */
    public $timezone;

    /**
     * Create a new resource instance.
     *
     * @param array $attributes
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
