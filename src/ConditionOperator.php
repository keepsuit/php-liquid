<?php

namespace Keepsuit\Liquid;

enum ConditionOperator
{
    case Equal;
    case NotEqual;
    case GreaterThan;
    case GreaterThanOrEqual;
    case LessThan;
    case LessThanOrEqual;
    case Contains;

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
        if ($this === ConditionOperator::Contains) {
            return $this->contains($left, $right);
        }

        if (gettype($left) !== gettype($right)) {
            $this->throwCompareTypesException($left, $right);
        }

        return match ($this) {
            ConditionOperator::Equal => $left == $right,
            ConditionOperator::NotEqual => $left != $right,
            ConditionOperator::GreaterThan => $left > $right,
            ConditionOperator::GreaterThanOrEqual => $left >= $right,
            ConditionOperator::LessThan => $left < $right,
            ConditionOperator::LessThanOrEqual => $left <= $right,
        };
    }

    protected function contains(mixed $left, mixed $right): bool
    {
        return match (gettype($left)) {
            'array' => in_array($right, $left, true),
            'string' => assert(is_numeric($right) || is_string($right)) && str_contains($left, (string) $right),
            default => false,
        };
    }

    protected function throwCompareTypesException(mixed $left, mixed $right): void
    {
        throw new \RuntimeException(sprintf('Cannot compare %s with %s', gettype($left), gettype($right)));
    }
}
