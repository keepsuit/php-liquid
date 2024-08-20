<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;

class UndefinedVariable implements AsLiquidValue, CanBeRendered
{
    public function __construct(public readonly string $variableName) {}

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
