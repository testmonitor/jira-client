<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\Webhook;
use TestMonitor\Jira\Transforms\TransformsWebhooks;
use TestMonitor\Jira\Exceptions\FailedActionException;

trait ManagesWebhooks
{
    use TransformsWebhooks;

    /**
     * Get a list of webhooks.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return array<\TestMonitor\Jira\Resources\Webhook>
     */
    public function webhooks(int $offset = 0, int $limit = 50): array
    {
        $response = $this->get('webhook', [
            'query' => [
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return $this->fromJiraWebhooks($response['values'] ?? []);
    }

    /**
     * Create a new webhook.
     */
    public function createWebhook(Webhook $webhook): Webhook
    {
        $response = $this->post('webhook', ['json' => $this->toJiraWebhook($webhook)]);

        if (array_key_exists('errors', $response['webhookRegistrationResult'][0])) {
            throw new FailedActionException($response['webhookRegistrationResult'][0]['errors'][0]);
        }

        $webhook->id = $response['webhookRegistrationResult'][0]['createdWebhookId'];

        return $webhook;
    }

    /**
     * Extend webhook lifetimes.
     */
    public function extendWebhookLifetimes(array $webhookIds): string
    {
        $response = $this->put('webhook/refresh', [
            'json' => ['webhookIds' => $webhookIds],
        ]);

        return $response['expirationDate'];
    }

    /**
     * Delete webhooks.
     */
    public function deleteWebhooks(array $webhookIds): bool
    {
        $response = $this->delete('webhook', [
            'json' => ['webhookIds' => $webhookIds],
        ]);

        return empty($response);
    }
}
