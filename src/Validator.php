<?php

namespace TestMonitor\Jira;

use TestMonitor\Jira\Exceptions\InvalidDataException;

class Validator
{
    /**
     * @param mixed $subject
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function isInteger($subject)
    {
        if (! is_integer($subject)) {
            throw new InvalidDataException($subject);
        }

        return true;
    }

    /**
     * @param mixed $subject
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function isString($subject)
    {
        if (! is_string($subject)) {
            throw new InvalidDataException($subject);
        }

        return true;
    }

    /**
     * @param mixed $subject
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function isArray($subject)
    {
        if (! is_array($subject)) {
            throw new InvalidDataException($subject);
        }

        return true;
    }

    /**
     * @param mixed $haystack
     * @param mixed $needle
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function keyExists($haystack, $needle)
    {
        if (! array_key_exists($needle, $haystack)) {
            throw new InvalidDataException($haystack);
        }

        return true;
    }

    /**
     * @param mixed $haystack
     * @param array $needles
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function keysExists($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            self::keyExists($haystack, $needle);
        }

        return true;
    }

    /**
     * @param mixed $object
     * @param string $property
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function hasProperty($object, $property)
    {
        if (! property_exists($object, $property)) {
            throw new \TestMonitor\Jira\Exceptions\InvalidDataException($object);
        }

        return true;
    }

    /**
     * @param mixed $object
     * @param array $properties
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     * @return bool
     */
    public static function hasProperties($object, array $properties)
    {
        foreach ($properties as $property) {
            self::hasProperty($object, $property);
        }

        return true;
    }
}
