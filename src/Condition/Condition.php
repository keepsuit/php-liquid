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

    public ?BodyNode $body = null;

    public function __construct(
        protected mixed $left = null,
        protected ?string $operator = null,
        protected mixed $right = null
    ) {}

    public function export(CompilerContext $context): ?string
    {
        // A chained condition would need statements rather than an expression,
        // and a subclass need not accept these constructor arguments.
        if ($this->childCondition !== null || static::class !== self::class) {
            return null;
        }

        // The body is deliberately left out: the compiler emits it as code and
        // only ever calls evaluate() on the rebuilt condition.
        return 'new \\'.self::class.'('
            .$context->writeValue($this->left).', '
            .$context->writeValue($this->operator).', '
            .$context->writeValue($this->right).')';
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

        return ConditionOperator::parse($operator)->evaluate($left, $right);
    }

    protected function toLiquidValue(mixed $value): mixed
    {
        if ($value instanceof AsLiquidValue) {
            return $value->toLiquidValue();
        }

        return $value;
    }
}
