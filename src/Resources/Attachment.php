<?php

namespace TestMonitor\Jira\Resources;

use TestMonitor\Jira\Validator;

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
        Validator::keysExists($attachment, ['id', 'filename', 'content']);

        $this->id = $attachment['id'];
        $this->filename = $attachment['filename'];
        $this->content = $attachment['content'];
    }
}
