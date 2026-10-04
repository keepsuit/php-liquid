<?php

namespace Keepsuit\Liquid\Condition;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;

class Condition implements CanBeExported, HasParseTreeVisitorChildren
{
    /**
     * @var array<string, \Closure>
     */
    protected static array $customOperators = [];

    protected ?ConditionsRelation $childRelation = null;

    protected ?Condition $childCondition = null;

    protected ?ConditionOperator $parsedOperator = null;

    public ?BodyNode $body = null;

    public function __construct(
        protected mixed $left = null,
        protected ?string $operator = null,
        protected mixed $right = null
    ) {}

    public function export(CompilerContext $context): ?string
    {
        $conditions = [];
        $seen = [];
        for ($condition = $this; $condition !== null; $condition = $condition->childCondition) {
            // Preserve custom constructors/evaluators and cyclic object graphs
            // through the existing serialization fallback.
            $id = spl_object_id($condition);
            if ($condition::class !== self::class || isset($seen[$id])) {
                return null;
            }
            $seen[$id] = true;
            $conditions[] = $condition;
        }

        // The body is deliberately left out: the compiler emits it as code and
        // only ever calls evaluate() on the rebuilt condition.
        $source = null;
        for ($index = count($conditions) - 1; $index >= 0; $index--) {
            $condition = $conditions[$index];
            $parent = 'new '.$context->writeClassName(self::class).'('
                .$context->writeValue($condition->left).', '
                .$context->writeValue($condition->operator).', '
                .$context->writeValue($condition->right).')';
            $relation = $condition->childRelation === null
                ? 'null'
                : $context->writeClassName(ConditionsRelation::class).'::'.$condition->childRelation->name;
            $source = $source === null ? $parent
                : $context->writeClassName(self::class).'::chain('.$parent.', '.$relation.', '.$source.')';
        }

        return $source;
    }

    /** @internal */
    public static function chain(self $condition, ?ConditionsRelation $relation, self $child): self
    {
        $condition->childRelation = $relation;
        $condition->childCondition = $child;

        return $condition;
    }

    /** @internal */
    public function compileExpression(CompilerContext $context): string
    {
        $conditions = [];
        $seen = [];
        for ($condition = $this; $condition !== null; $condition = $condition->childCondition) {
            $id = spl_object_id($condition);
            if ($condition::class !== self::class || isset($seen[$id])) {
                return $context->writeRuntimeValue($this).'->evaluate($context)';
            }
            $seen[$id] = true;
            $conditions[] = $condition;
        }

        $source = '';
        for ($index = count($conditions) - 1; $index >= 0; $index--) {
            $condition = $conditions[$index];
            $left = $context->writeEvaluatedExpression($condition->left);
            if ($condition->operator === null) {
                $parent = is_scalar($condition->left) || $condition->left === null
                    ? $context->writeValue($condition->left !== false && $condition->left !== null)
                    : '$this->conditionTruthy('.$left.')';
            } else {
                try {
                    $operator = ConditionOperator::parse($condition->operator);
                } catch (\Keepsuit\Liquid\Exceptions\SyntaxException) {
                    $operator = null;
                }
                // Normalize the left value before evaluating the right value:
                // AsLiquidValue implementations may have observable effects.
                $parent = $context->writeClassName(self::class).'::compare('
                    .'$this->conditionValue('.$left.'), '
                    .'$this->conditionValue('.$context->writeEvaluatedExpression($condition->right).'), '
                    .$context->writeValue($condition->operator).', '.$context->writeValue($operator).')';
            }

            $source = match (true) {
                $source === '' => $parent,
                $condition->childRelation === ConditionsRelation::And => '('.$parent.' && '.$source.')',
                $condition->childRelation === ConditionsRelation::Or => '('.$parent.' || '.$source.')',
                default => $parent,
            };
        }

        return $source;
    }

    /** @internal */
    public static function compare(mixed $left, mixed $right, string $operator, ?ConditionOperator $parsedOperator): bool
    {
        if (array_key_exists($operator, self::$customOperators)) {
            return (bool) self::$customOperators[$operator]($left, $right);
        }

        return ($parsedOperator ?? ConditionOperator::parse($operator))->evaluate($left, $right);
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

    public function body(?BodyNode $body): Condition
    {
        $this->body = $body;

        return $this;
    }

    public function else(): bool
    {
        return false;
    }

    public function evaluate(RenderContext $context): bool
    {
        $result = $this->interpretCondition($this->left, $this->right, $this->operator, $context);

        if ($this->childCondition === null) {
            return $result;
        }

        return match ($this->childRelation) {
            ConditionsRelation::Or => $result || $this->childCondition->evaluate($context),
            ConditionsRelation::And => $result && $this->childCondition->evaluate($context),
            default => $result,
        };
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            $this->left,
            $this->right,
            $this->childCondition,
            $this->body,
        ]);
    }

    protected function interpretCondition(mixed $left, mixed $right, ?string $operator, RenderContext $context): bool
    {
        if ($operator === null) {
            $result = $this->toLiquidValue($context->evaluate($left));

            return $result !== false && $result !== null;
        }

        $left = $this->toLiquidValue($context->evaluate($left));
        $right = $this->toLiquidValue($context->evaluate($right));

        if (array_key_exists($operator, static::$customOperators)) {
            return (bool) static::$customOperators[$operator]($left, $right);
        }

        return ($this->parsedOperator ??= ConditionOperator::parse($operator))->evaluate($left, $right);
    }

    protected function toLiquidValue(mixed $value): mixed
    {
        if ($value instanceof AsLiquidValue) {
            return $value->toLiquidValue();
        }

        return $value;
    }
}
