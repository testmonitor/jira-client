<?php

namespace TestMonitor\Jira\Resources;

use JqlBuilder\Jql;

class Webhook extends Resource
{
    /**
     * The id of the webhook.
     *
     * @var string
     */
    public $id;

    /**
     * The URL of the webhook.
     *
     * @var string
     */
    public $url;

    /**
     * The webhook JQL filter.
     *
     * @var string
     */
    public $jqlFilter;

    /**
     * The events of the webhook.
     *
     * @var array
     */
    public $events;

    /**
     * The expiration date of the webhook.
     *
     * @var string
     */
    public $expirationDate;

    /**
     * Create a new resource instance.
     *
     * @param array $webhook
     */
    public function __construct(array $webhook)
    {
        $this->id = $webhook['id'] ?? '';
        $this->url = $webhook['url'] ?? '';
        $this->events = $webhook['events'];

        $this->jqlFilter = isset($webhook['projectIds']) ?
            $this->projectFilter($webhook['projectIds'])->getQuery() :
            $webhook['jqlFilter'] ?? '';

        $this->expirationDate = $webhook['expirationDate'] ?? '';
    }

    /**
     * Returns a project filter.
     *
     * @param string[] $projectIds
     * @return \JqlBuilder\Jql
     */
    protected function projectFilter($projectIds): Jql
    {
        return (new Jql)->where(column: 'project', operator: 'in', value: $projectIds);
    }
}
