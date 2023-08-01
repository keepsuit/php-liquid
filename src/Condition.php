<?php

namespace Keepsuit\Liquid;

class Condition implements HasParseTreeVisitorChildren
{
    /**
     * @var array<string, \Closure>
     */
    protected static array $customOperators = [];

    protected ?ConditionsRelation $childRelation = null;

    protected ?Condition $childCondition = null;

    public ?BlockBody $attachment = null;

    public function __construct(
        protected mixed $left = null,
        protected ?string $operator = null,
        protected mixed $right = null
    ) {
    }

    public static function registerOperator(string $operator, \Closure $closure): void
    {
        static::$customOperators[$operator] = $closure;
    }

    public static function deleteOperator(string $operator): void
    {
        unset(static::$customOperators[$operator]);
    }

    public static function resetOperators(): void
    {
        static::$customOperators = [];
    }

    public function and(Condition $childCondition): Condition
    {
        $this->childRelation = ConditionsRelation::And;
        $this->childCondition = $childCondition;

        return $childCondition;
    }

    public function or(Condition $childCondition): Condition
    {
        $this->childRelation = ConditionsRelation::Or;
        $this->childCondition = $childCondition;

        return $childCondition;
    }

    public function attach(?BlockBody $body): Condition
    {
        $this->attachment = $body;

        return $this;
    }

    public function evaluate(Context $context): bool
    {
        $condition = $this;
        $result = null;

        while ($condition !== null) {
            $result = $this->interpretCondition($condition->left, $condition->right, $condition->operator, $context);

            if ($condition->childRelation === null || $condition->childCondition === null) {
                break;
            }
            if ($condition->childRelation === ConditionsRelation::Or && $result === true) {
                break;
            }
            if ($condition->childRelation === ConditionsRelation::And && $result === false) {
                break;
            }

            $condition = $condition->childCondition;
        }

        return $result;
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            $this->left,
            $this->right,
            $this->childCondition,
            $this->attachment,
        ]);
    }

    protected function interpretCondition(mixed $left, mixed $right, ?string $operator, Context $context): bool
    {
        if ($operator === null) {
            return (bool) $context->evaluate($left);
        }

        $left = $context->evaluate($left);
        $right = $context->evaluate($right);

        if (array_key_exists($operator, static::$customOperators)) {
            return (bool) static::$customOperators[$operator]($left, $right);
        }

        return ConditionOperator::parse($operator)->evaluate($left, $right);
    }
}
