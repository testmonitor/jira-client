<?php

namespace TestMonitor\Jira\Resources;

use TestMonitor\Jira\Validator;

class Attachment extends Resource
{
    /**
     * The ID of the attachment.
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
     * The mimetype of the attachment.
     *
     * @var string
     */
    public $mimeType;

    /**
     * The size of the attachment.
     *
     * @var integer
     */
    public $size;

    /**
     * Create a new resource instance.
     *
     * @param array $attachment
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
