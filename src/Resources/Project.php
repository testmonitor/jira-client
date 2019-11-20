<?php

namespace TestMonitor\Jira\Resources;

class Project extends Resource
{
    /**
     * The id of the project.
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
     * The issue types of the project.
     *
     * @var array
     */
    public $issueTypes;

    /**
     * Create a new resource instance.
     *
     * @param $id
     * @param string $key
     * @param string $name
     * @param array $issueTypes
     */
    public function __construct($id, string $key, string $name, array $issueTypes = [])
    {
        $this->id = $id;
        $this->key = $key;
        $this->name = $name;

        $this->issueTypes = $issueTypes;
    }
}
