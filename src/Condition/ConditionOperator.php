<?php

namespace Keepsuit\Liquid\Condition;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Literal;
use Keepsuit\Liquid\Nodes\Range;

enum ConditionOperator
{
    case Equal;
    case NotEqual;
    case GreaterThan;
    case GreaterThanOrEqual;
    case LessThan;
    case LessThanOrEqual;
    case Contains;

    /**
     * @throws SyntaxException
     */
    public static function parse(string $operator): ConditionOperator
    {
        return match ($operator) {
            '==' => ConditionOperator::Equal,
            '!=', '<>' => ConditionOperator::NotEqual,
            '>' => ConditionOperator::GreaterThan,
            '>=' => ConditionOperator::GreaterThanOrEqual,
            '<' => ConditionOperator::LessThan,
            '<=' => ConditionOperator::LessThanOrEqual,
            'contains' => ConditionOperator::Contains,
            default => throw new SyntaxException("Unknown operator: $operator"),
        };
    }

    public function evaluate(mixed $left, mixed $right): bool
    {
        if ($this === ConditionOperator::Equal || $this === ConditionOperator::NotEqual) {
            return $this->regularEvaluation($left, $right);
        }

        if ($left === null || $right === null) {
            return false;
        }

        // PHP compares numeric strings numerically; Liquid compares them lexically.
        if (is_string($left) && is_string($right) && $this !== ConditionOperator::Contains) {
            return $this->regularEvaluation(strcmp($left, $right), 0);
        }

        if (gettype($left) === gettype($right)) {
            return $this->regularEvaluation($left, $right);
        }

        if ($this->isNumberOrNumericString($left) && $this->isNumberOrNumericString($right)) {
            return match ($this) {
                ConditionOperator::Contains => $this->evaluateContains((string) $left, (string) $right),
                default => $this->regularEvaluation($left + 0, $right + 0)
            };
        }

        return match ($this) {
            ConditionOperator::Contains => $this->regularEvaluation($left, $right),
            default => $this->throwCompareTypesException($left, $right),
        };
    }

    protected function regularEvaluation(mixed $left, mixed $right): bool
    {
        return match ($this) {
            ConditionOperator::Equal => $this->evaluateEqual($left, $right),
            ConditionOperator::NotEqual => ! $this->evaluateEqual($left, $right),
            ConditionOperator::GreaterThan => $left > $right,
            ConditionOperator::GreaterThanOrEqual => $left >= $right,
            ConditionOperator::LessThan => $left < $right,
            ConditionOperator::LessThanOrEqual => $left <= $right,
            ConditionOperator::Contains => $this->evaluateContains($left, $right),
        };
    }

    protected function evaluateEqual(mixed $left, mixed $right): bool
    {
        if ($left === $right) {
            return true;
        }

        if ((is_int($left) || is_float($left)) && (is_int($right) || is_float($right))) {
            return $left == $right;
        }

        if ($left instanceof Range && $right instanceof Range) {
            return $left->start === $right->start && $left->end === $right->end;
        }

        [$left, $right] = match (true) {
            $right instanceof Literal => [$right, $left],
            default => [$left, $right],
        };

        if ($left instanceof Literal) {
            return match ($left) {
                Literal::Empty => $right === '' || $this->isEmptyIterable($right),
                Literal::Blank => $right === null || $right === false || $this->isEmptyIterable($right) || (is_string($right) && trim($right) === ''),
            };
        }

        return false;
    }

    protected function isEmptyIterable(mixed $value): bool
    {
        if (! is_iterable($value)) {
            return false;
        }

        foreach ($value as $_) {
            return false;
        }

        return true;
    }

    protected function evaluateContains(mixed $left, mixed $right): bool
    {
        return match (true) {
            is_array($left) => array_is_list($left)
                ? in_array($right, $left, true)
                : ((is_string($right) || is_int($right)) && array_key_exists($right, $left)),
            is_string($left) => assert(is_numeric($right) || is_string($right)) && str_contains($left, (string) $right),
            $left instanceof Range => (is_int($right) || is_float($right)) && $left->start <= $right && $right <= $left->end,
            default => false,
        };
    }

    /**
     * @return never-returns
     */
    protected function throwCompareTypesException(mixed $left, mixed $right): void
    {
        throw new \RuntimeException(sprintf('Cannot compare %s with %s', gettype($left), gettype($right)));
    }

    /**
     * @phpstan-assert-if-true numeric-string|int|float $value
     */
    protected function isNumberOrNumericString(mixed $value): bool
    {
        return match (gettype($value)) {
            'integer', 'double' => true,
            'string' => is_numeric($value),
            default => false,
        };
    }
}
