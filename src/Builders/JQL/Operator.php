<?php

namespace TestMonitor\Jira\Builders\JQL;

/**
 * @see https://support.atlassian.com/jira-software-cloud/docs/advanced-search-reference-jql-operators
 */
class Operator
{
    public const string EQUALS = '=';

    public const string NOT_EQUALS = '!=';

    public const string GREATER_THAN = '>';

    public const string GREATER_THAN_EQUALS = '>=';

    public const string LESS_THAN = '<';

    public const string LESS_THAN_EQUALS = '<=';

    public const string IN = 'in';

    public const string NOT_IN = 'not in';

    public const string CONTAINS = '~';

    public const string DOES_NOT_CONTAIN = '!~';

    public const string IS = 'is';

    public const string IS_NOT = 'is not';

    public const string WAS = 'was';

    public const string WAS_IN = 'was in';

    public const string WAS_NOT_IN = 'was not in';

    public const string WAS_NOT = 'was not';

    public const string CHANGED = 'changed';

    /**
     * Returns the list of operators that accept a list of values.
     *
     * @return string[]
     */
    public static function acceptList(): array
    {
        return [
            self::IN,
            self::NOT_IN,
            self::WAS_IN,
            self::WAS_NOT_IN,
        ];
    }
}
