<?php

namespace TestMonitor\Jira\Actions;

use JiraRestApi\JiraException;
use TestMonitor\Jira\Exceptions\Exception;
use TestMonitor\Jira\Transforms\TransformsAttachments;

trait ManagesAttachments
{
    use TransformsAttachments;

    /**
     * Add a new attachment.
     *
     * @param string $path
     * @param string $issueKey
     *
     * @throws \TestMonitor\Jira\Exceptions\Exception
     * @return \TestMonitor\Jira\Resources\Attachment
     */
    public function addAttachment(string $path, string $issueKey)
    {
        try {
            $result = $this->issueService()->addAttachments($issueKey, [$path]);

            return $this->fromJiraAttachment($result[0]);
        } catch (JiraException $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
