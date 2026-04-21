<?php

namespace TestMonitor\Jira\Exceptions;

class InvalidDataException extends Exception
{
    protected mixed $data;

    /**
     * Create a new invalid data exception instance.
     */
    public function __construct(mixed $data)
    {
        parent::__construct('The given data contains invalid data and cannot be decoded.');

        $this->data = $data;
    }

    /**
     * Returns the invalid data.
     */
    public function data(): mixed
    {
        return $this->data;
    }
}
