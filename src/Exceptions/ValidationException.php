<?php

namespace TestMonitor\Jira\Exceptions;

class ValidationException extends Exception
{
    protected array $errors;

    /**
     * Create a new validation exception instance.
     */
    public function __construct(array $errors)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->errors = $errors;
    }

    /**
     * Returns the validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
