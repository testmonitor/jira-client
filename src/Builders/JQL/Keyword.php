<?php

namespace TestMonitor\Jira\Builders\JQL;

/**
 * @see https://support.atlassian.com/jira-software-cloud/docs/advanced-search-reference-jql-keywords
 */
class Keyword
{
    public const string AND = 'and';

    public const string OR = 'or';

    public const string NOT = 'not';

    public const string EMPTY = 'empty';

    public const string NULL = 'null';

    public const string ORDER_BY = 'order by';

    /**
     * Returns the list of boolean keywords.
     *
     * @return string[]
     */
    public static function booleans(): array
    {
        return [
            self::AND,
            self::OR,
        ];
    }
}
