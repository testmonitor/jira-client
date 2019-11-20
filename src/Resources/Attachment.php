<?php

namespace TestMonitor\Jira\Resources;

class Attachment extends Resource
{
    /**
     * The id of the attachment.
     *
     * @var string
     */
    public $id;

    /**
     * The filename of the attachment.
     *
     * @var string
     */
    public $filename;

    /**
     * The URL of the attachment.
     *
     * @var string
     */
    public $content;

    /**
     * Create a new resource instance.
     *
     * @param string $id
     * @param string $filename
     * @param string $content
     */
    public function __construct(string $id, string $filename, string $content)
    {
        $this->id = $id;
        $this->filename = $filename;
        $this->content = $content;
    }
}
