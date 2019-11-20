<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Resources\Attachment;

trait TransformsAttachments
{
    /**
     * @param \JiraRestApi\Issue\Attachment $attachment
     *
     * @return \TestMonitor\Jira\Resources\Attachment
     */
    protected function fromJiraAttachment(\JiraRestApi\Issue\Attachment $attachment): Attachment
    {
        return new Attachment(
            $attachment->id,
            $attachment->filename,
            $attachment->content
        );
    }
}
