<?php

namespace TestMonitor\Jira\Transforms;

use TestMonitor\Jira\Validator;
use TestMonitor\Jira\Resources\Webhook;

trait TransformsWebhooks
{
    protected function toJiraWebhook(Webhook $webhook): array
    {
        return [
            'url' => $webhook->url,
            'webhooks' => [
                [
                    'events' => $webhook->events,
                    'jqlFilter' => $webhook->jqlFilter,
                ],
            ],
        ];
    }

    /**
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\Webhook>
     */
    protected function fromJiraWebhooks(mixed $webhooks): array
    {
        Validator::isArray($webhooks);

        return array_map(function ($webhook) {
            return $this->fromJiraWebhook($webhook);
        }, $webhooks);
    }

    protected function fromJiraWebhook(array $webhook): Webhook
    {
        return new Webhook([
            'id' => $webhook['id'],
            'url' => $webhook['url'] ?? '',
            'jqlFilter' => $webhook['jqlFilter'] ?? '',
            'events' => $webhook['events'] ?? [],
            'expirationDate' => $webhook['expirationDate'] ?? '',
        ]);
    }
}
