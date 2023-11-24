<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsAttachments;

trait ManagesAttachments
{
    use TransformsAttachments;

    /**
     * Get the attachment content.
     *
     * @param string $id
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Attachment
     */
    public function attachment($id)
    {
        return $this->get("attachment/content/{$id}");
    }

    /**
     * Add attachment to issue.
     *
     * @param string $issueId
     * @param string $path
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Attachment
     */
    public function addAttachmentToIssue(string $issueId, string $path)
    {
        $response = $this->post("issue/{$issueId}/attachments", [
            'headers' => [
                'X-Atlassian-Token' => 'no-check',
                'Accept' => 'application/json',
            ],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($path, 'r'),
                ],
            ],
        ]);

        return $this->fromJiraAttachments($response);
    }
}
