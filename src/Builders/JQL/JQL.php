<?php

namespace TestMonitor\Jira\Builders\JQL;

use Closure;
use Stringable;
use InvalidArgumentException;

class JQL implements Stringable
{
    protected string $query = '';

    /**
     * Add a where clause to the query.
     *
     * @throws \InvalidArgumentException
     */
    public function where(
        string|Closure $column,
        mixed $operator = Operator::EQUALS,
        mixed $value = null,
        string $boolean = Keyword::AND
    ): self {
        if ($column instanceof Closure) {
            $prefix = $this->getQuery() === ''
                ? ''
                : "{$this->getQuery()} {$boolean} ";

            $this->query = '';
            $column($this);
            $this->query = "{$prefix}({$this->getQuery()})";

            return $this;
        }

        if (func_num_args() === 2) {
            [$column, $operator, $value] = [$column, is_array($operator) ? Operator::IN : Operator::EQUALS, $operator];
        }

        /** @var string $operator */
        $this->validateBooleanAndOperator($boolean, $operator, $value);

        $this->appendQuery("{$this->escapeSpaces($column)} {$operator} {$this->quote($operator, $value)}", $boolean);

        return $this;
    }

    /**
     * Add an or where clause to the query.
     *
     * @throws \InvalidArgumentException
     */
    public function orWhere(string|Closure $column, mixed $operator = Operator::EQUALS, mixed $value = null): self
    {
        if (func_num_args() === 2) {
            [$column, $operator, $value] = [$column, is_array($operator) ? Operator::IN : Operator::EQUALS, $operator];
        }

        return $this->where($column, $operator, $value, Keyword::OR);
    }

    /**
     * Execute a callback when the value is truthy.
     */
    public function when(mixed $value, callable $callback): self
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if ($value) {
            $callback($this, $value);
        }

        return $this;
    }

    /**
     * Execute a callback when the value is falsy.
     */
    public function whenNot(mixed $value, callable $callback): self
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (! $value) {
            $callback($this, $value);
        }

        return $this;
    }

    /**
     * Add an order by clause to the query.
     */
    public function orderBy(string $column, string $direction): self
    {
        $this->appendQuery(Keyword::ORDER_BY . " {$this->escapeSpaces($column)} {$direction}");

        return $this;
    }

    /**
     * Add a raw query string.
     */
    public function rawQuery(string $query): self
    {
        $this->appendQuery($query);

        return $this;
    }

    /**
     * Reset the query.
     */
    public function reset(): self
    {
        $this->query = '';

        return $this;
    }

    /**
     * Get the current query string.
     */
    public function getQuery(): string
    {
        return trim($this->query);
    }

    /**
     * Returns the query as a string.
     */
    public function __toString(): string
    {
        return $this->getQuery();
    }

    protected function escapeSpaces(string $column): string
    {
        return str_contains($column, ' ') ? "\"{$column}\"" : $column;
    }

    protected function quote(string $operator, mixed $value): string
    {
        if (in_array($operator, Operator::acceptList(), true)) {
            $items = array_map(
                fn ($item) => '"' . str_replace('"', '\\"', (string) $item) . '"',
                is_array($value) ? $value : [$value]
            );

            return '(' . implode(', ', $items) . ')';
        }

        return '"' . str_replace('"', '\\"', (string) $value) . '"';
    }

    protected function appendQuery(string $query, string $boolean = ''): void
    {
        $this->query = $this->getQuery() === ''
            ? $query
            : $this->query . ' ' . trim("{$boolean} {$query}");
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validateBooleanAndOperator(string $boolean, string $operator, mixed $value): void
    {
        if (! in_array($boolean, Keyword::booleans(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Illegal boolean [%s] value. Only [%s, %s] are acceptable.',
                $boolean,
                ...Keyword::booleans(),
            ));
        }

        if (is_array($value) && ! in_array($operator, Operator::acceptList(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Illegal operator [%s] value. Only [%s, %s, %s, %s] are acceptable when $value is an array.',
                $operator,
                ...Operator::acceptList(),
            ));
        }
    }
}
