<?php

namespace TestMonitor\Jira;

use TestMonitor\Jira\Exceptions\InvalidDataException;

class Validator
{
    /**
     * Validates that the subject is an integer.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public static function isInteger(mixed $subject): true
    {
        if (! is_integer($subject)) {
            throw new InvalidDataException($subject);
        }

        return true;
    }

    /**
     * Validates that the subject is a string.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public static function isString(mixed $subject): true
    {
        if (! is_string($subject)) {
            throw new InvalidDataException($subject);
        }

        return true;
    }

    /**
     * Validates that the subject is an array.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public static function isArray(mixed $subject): true
    {
        if (! is_array($subject)) {
            throw new InvalidDataException($subject);
        }

        return true;
    }

    /**
     * Validates that the given key exists in the haystack.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public static function keyExists(mixed $haystack, mixed $needle): true
    {
        if (! array_key_exists($needle, $haystack)) {
            throw new InvalidDataException($haystack);
        }

        return true;
    }

    /**
     * Validates that all given keys exist in the haystack.
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     */
    public static function keysExists(mixed $haystack, array $needles): true
    {
        foreach ($needles as $needle) {
            self::keyExists($haystack, $needle);
        }

        return true;
    }
}
