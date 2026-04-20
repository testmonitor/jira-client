<?php

namespace TestMonitor\Jira\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Jira\Builders\JQL\JQL;
use TestMonitor\Jira\Builders\JQL\Field;
use TestMonitor\Jira\Builders\JQL\Keyword;
use TestMonitor\Jira\Builders\JQL\Operator;

class JQLTest extends TestCase
{
    #[Test]
    public function it_should_build_a_simple_where_clause()
    {
        // When
        $query = (new JQL())->where(Field::STATUS, Operator::EQUALS, 'Open');

        // Then
        $this->assertEquals('status = "Open"', (string) $query);
    }

    #[Test]
    public function it_should_build_a_where_clause_using_two_arguments()
    {
        // When
        $query = (new JQL())->where(Field::STATUS, 'Open');

        // Then
        $this->assertEquals('status = "Open"', (string) $query);
    }

    #[Test]
    public function it_should_build_a_where_in_clause_when_passing_an_array_as_second_argument()
    {
        // When
        $query = (new JQL())->where(Field::STATUS, ['Open', 'In Progress']);

        // Then
        $this->assertEquals('status in ("Open", "In Progress")', (string) $query);
    }

    #[Test]
    public function it_should_build_a_where_in_clause_with_an_explicit_in_operator()
    {
        // When
        $query = (new JQL())->where(Field::PROJECT, Operator::IN, ['FOO', 'BAR']);

        // Then
        $this->assertEquals('project in ("FOO", "BAR")', (string) $query);
    }

    #[Test]
    public function it_should_build_a_where_not_in_clause()
    {
        // When
        $query = (new JQL())->where(Field::PROJECT, Operator::NOT_IN, ['FOO', 'BAR']);

        // Then
        $this->assertEquals('project not in ("FOO", "BAR")', (string) $query);
    }

    #[Test]
    public function it_should_chain_multiple_where_clauses_with_and()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->where(Field::STATUS, 'Open');

        // Then
        $this->assertEquals('project = "FOO" and status = "Open"', (string) $query);
    }

    #[Test]
    public function it_should_build_an_or_where_clause()
    {
        // When
        $query = (new JQL())
            ->where(Field::STATUS, 'Open')
            ->orWhere(Field::STATUS, 'In Progress');

        // Then
        $this->assertEquals('status = "Open" or status = "In Progress"', (string) $query);
    }

    #[Test]
    public function it_should_build_an_or_where_clause_using_two_arguments()
    {
        // When
        $query = (new JQL())
            ->where(Field::STATUS, 'Open')
            ->orWhere(Field::STATUS, ['In Progress', 'Done']);

        // Then
        $this->assertEquals('status = "Open" or status in ("In Progress", "Done")', (string) $query);
    }

    #[Test]
    public function it_should_group_clauses_using_a_closure()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->where(function (JQL $jql) {
                $jql->where(Field::STATUS, 'Open')
                    ->orWhere(Field::STATUS, 'In Progress');
            });

        // Then
        $this->assertEquals('project = "FOO" and (status = "Open" or status = "In Progress")', (string) $query);
    }

    #[Test]
    public function it_should_start_a_query_with_a_grouped_closure()
    {
        // When
        $query = (new JQL())->where(function (JQL $jql) {
            $jql->where(Field::STATUS, 'Open')
                ->orWhere(Field::STATUS, 'In Progress');
        });

        // Then
        $this->assertEquals('(status = "Open" or status = "In Progress")', (string) $query);
    }

    #[Test]
    public function it_should_apply_a_callback_when_value_is_truthy()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->when('Open', function (JQL $jql, $value) {
                $jql->where(Field::STATUS, $value);
            });

        // Then
        $this->assertEquals('project = "FOO" and status = "Open"', (string) $query);
    }

    #[Test]
    public function it_should_skip_the_callback_when_value_is_falsy()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->when(null, function (JQL $jql) {
                $jql->where(Field::STATUS, 'Open');
            });

        // Then
        $this->assertEquals('project = "FOO"', (string) $query);
    }

    #[Test]
    public function it_should_apply_a_callback_when_value_is_falsy_using_when_not()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->whenNot(null, function (JQL $jql) {
                $jql->where(Field::STATUS, 'Open');
            });

        // Then
        $this->assertEquals('project = "FOO" and status = "Open"', (string) $query);
    }

    #[Test]
    public function it_should_skip_the_callback_when_value_is_truthy_using_when_not()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->whenNot('something', function (JQL $jql) {
                $jql->where(Field::STATUS, 'Open');
            });

        // Then
        $this->assertEquals('project = "FOO"', (string) $query);
    }

    #[Test]
    public function it_should_add_an_order_by_clause()
    {
        // When
        $query = (new JQL())
            ->where(Field::PROJECT, 'FOO')
            ->orderBy(Field::CREATED, 'DESC');

        // Then
        $this->assertEquals('project = "FOO" order by created DESC', (string) $query);
    }

    #[Test]
    public function it_should_add_a_raw_query()
    {
        // When
        $query = (new JQL())->rawQuery('project = "FOO" and status = "Open"');

        // Then
        $this->assertEquals('project = "FOO" and status = "Open"', (string) $query);
    }

    #[Test]
    public function it_should_reset_the_query()
    {
        // Given
        $jql = (new JQL())->where(Field::PROJECT, 'FOO');

        // When
        $jql->reset();

        // Then
        $this->assertEquals('', $jql->getQuery());
    }

    #[Test]
    public function it_should_escape_column_names_containing_spaces()
    {
        // When
        $query = (new JQL())->where('custom field', Operator::EQUALS, 'value');

        // Then
        $this->assertEquals('"custom field" = "value"', (string) $query);
    }

    #[Test]
    public function it_should_escape_double_quotes_in_values()
    {
        // When
        $query = (new JQL())->where(Field::SUMMARY, Operator::CONTAINS, 'say "hello"');

        // Then
        $this->assertEquals('summary ~ "say \"hello\""', (string) $query);
    }

    #[Test]
    public function it_should_throw_an_exception_when_using_an_invalid_boolean()
    {
        // Given
        $this->expectException(InvalidArgumentException::class);

        // When
        (new JQL())->where(Field::STATUS, Operator::EQUALS, 'Open', 'invalid');
    }

    #[Test]
    public function it_should_throw_an_exception_when_passing_an_array_with_a_non_list_operator()
    {
        // Given
        $this->expectException(InvalidArgumentException::class);

        // When
        (new JQL())->where(Field::STATUS, Operator::EQUALS, ['Open', 'Closed']);
    }

    #[Test]
    public function it_should_return_the_list_of_boolean_keywords()
    {
        // When
        $booleans = Keyword::booleans();

        // Then
        $this->assertIsArray($booleans);
        $this->assertContains(Keyword::AND, $booleans);
        $this->assertContains(Keyword::OR, $booleans);
    }

    #[Test]
    public function it_should_return_the_list_of_operators_that_accept_a_list()
    {
        // When
        $operators = Operator::acceptList();

        // Then
        $this->assertIsArray($operators);
        $this->assertContains(Operator::IN, $operators);
        $this->assertContains(Operator::NOT_IN, $operators);
        $this->assertContains(Operator::WAS_IN, $operators);
        $this->assertContains(Operator::WAS_NOT_IN, $operators);
    }
}
