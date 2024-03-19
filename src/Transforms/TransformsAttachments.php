<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Attachment;

trait TransformsAttachments
{
    /**
     * @param array $attachments
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Attachment[]
     */
    protected function fromJiraAttachments($attachments): array
    {
        Validator::isArray($attachments);

        return array_map(function ($attachment) {
            return $this->fromJiraAttachment($attachment);
        }, $attachments);
    }

    /**
     * @return \TestMonitor\Jira\Resources\Attachment
     */
    protected function fromJiraAttachment(array $attachment): Attachment
    {
        return new Attachment([
            'id' => $attachment['id'],
            'filename' => $attachment['filename'] ?? '',
            'size' => $attachment['size'] ?? '',
            'mimeType' => $attachment['mimeType'] ?? '',
        ]);
    }
}
