<?php

namespace TestMonitor\Jira\Resources;

class Account extends Resource
{
    /**
     * The ID of the account.
     *
     * @var string
     */
    public $id;

    /**
     * The URL for the account.
     *
     * @var string
     */
    public $url;

    /**
     * The name of the account.
     *
     * @var string
     */
    public $name;

    /**
     * The scopes for the account.
     *
     * @var array
     */
    public $scopes;

    /**
     * The avatar URL of the account.
     *
     * @var string
     */
    public $avatarUrl;

    /**
     * Create a new resource instance.
     *
     * @param array $attributes
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
