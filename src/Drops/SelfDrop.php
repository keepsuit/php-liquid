<?php

namespace Keepsuit\Liquid\Drops;

use Keepsuit\Liquid\Render\RenderContext;

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
        $variables = $this->context->findVariables($name);

        return $variables[0] ?? null;
    }

    public function __isset(string $name): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return '';
    }
}
