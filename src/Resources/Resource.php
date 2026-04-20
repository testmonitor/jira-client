<?php

namespace TestMonitor\Jira\Resources;

class Resource
{
    /**
     * Returns the resource as an array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
