<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Transforms\TransformsAttachments;

trait ManagesAttachments
{
    use TransformsAttachments;

    /**
     * Get the attachment content.
     */
    public function attachment(string $id): mixed
    {
        return $this->get("attachment/content/{$id}");
    }

    /**
     * Add an attachment to an issue.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\Attachment>
     */
    public function addAttachmentToIssue(string $issueId, string $path): array
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
