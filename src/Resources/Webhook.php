<?php

namespace TestMonitor\Jira\Resources;

class Webhook extends Resource
{
    public string $id;

    public string $url;

    public string $jqlFilter;

    public array $events;

    public string $expirationDate;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $webhook)
    {
        $this->id = $webhook['id'] ?? '';
        $this->url = $webhook['url'] ?? '';
        $this->events = $webhook['events'];

        $this->jqlFilter = $webhook['jqlFilter'] ?? '';

        $this->expirationDate = $webhook['expirationDate'] ?? '';
    }
}
