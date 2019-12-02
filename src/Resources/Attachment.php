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
     * @param array $attachment
     */
    public function __construct(array $attachment)
    {
        $this->id = $attachment['id'];
        $this->filename = $attachment['filename'];
        $this->content = $attachment['content'];
    }
}
