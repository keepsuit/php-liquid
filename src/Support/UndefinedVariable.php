<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;

class UndefinedVariable implements CanBeRendered, AsLiquidValue
{
    public function __construct(protected string $variableName)
    {
    }

    public function render(RenderContext $context): string
    {
        if ($context->strictVariables) {
            throw new UndefinedVariableException($this->variableName);
        }

        return '';
    }

    public function toLiquidValue(): string|int|float|bool|null
    {
        return null;
    }
}
