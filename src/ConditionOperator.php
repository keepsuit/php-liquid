<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\SyntaxException;

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
        if ($left === null || $right === null) {
            return match ($this) {
                ConditionOperator::Equal, ConditionOperator::NotEqual => $this->regularEvaluation($left, $right),
                default => false,
            };
        }

        if (gettype($left) !== gettype($right)) {
            return match ($this) {
                ConditionOperator::Equal, ConditionOperator::NotEqual, ConditionOperator::Contains => $this->regularEvaluation($left, $right),
                default => $this->throwCompareTypesException($left, $right),
            };
        }

        return $this->regularEvaluation($left, $right);
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

        [$left, $right] = match (true) {
            $right instanceof Literal => [$right, $left],
            default => [$left, $right],
        };

        if ($left instanceof Literal) {
            return match ($left) {
                Literal::Empty => empty($right),
                default => false
            };
        }

        return false;
    }

    protected function evaluateContains(mixed $left, mixed $right): bool
    {
        return match (gettype($left)) {
            'array' => in_array($right, $left, true),
            'string' => assert(is_numeric($right) || is_string($right)) && str_contains($left, (string) $right),
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
}
