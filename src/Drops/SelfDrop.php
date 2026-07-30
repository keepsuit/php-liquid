<?php

namespace Keepsuit\Liquid\Drops;

use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\MissingValue;

/**
 * Proxy object that resolves property lookups through the current render context scope chain.
 * Intentionally does not implement IsContextAware: a SelfDrop passed across a render boundary
 * must keep its original context rather than being rebound to the partial's context.
 */
final class SelfDrop
{
    public function __construct(
        private readonly RenderContext $context
    ) {}

    public function __get(string $name): mixed
    {
        $variable = $this->context->findVariable($name);

        return $variable instanceof MissingValue ? null : $variable;
    }

    public function __isset(string $name): bool
    {
        $variable = $this->context->findVariable($name);

        return ! $variable instanceof MissingValue;
    }

    public function __toString(): string
    {
        return '';
    }
}
