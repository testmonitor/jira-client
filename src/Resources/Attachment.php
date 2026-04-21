<?php

namespace TestMonitor\Jira\Resources;

use TestMonitor\Jira\Validator;

class Attachment extends Resource
{
    public string $id;

    public string $filename;

    public string $mimeType;

    public int $size;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attachment)
    {
        Validator::keysExists($attachment, ['id', 'filename']);

        $this->id = $attachment['id'];
        $this->filename = $attachment['filename'];
        $this->size = $attachment['size'];
        $this->mimeType = $attachment['mimeType'];
    }
}
