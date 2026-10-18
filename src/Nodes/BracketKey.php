<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeExported;

/**
 * A `size`, `first` or `last` key written in brackets (`a["size"]`): it is looked up as a
 * plain key, never as the pseudo-property that dot notation (`a.size`) resolves to.
 */
final class BracketKey implements \Stringable, CanBeExported
{
    public function __construct(
        public readonly string $value,
    ) {}

    public function export(CompilerContext $context): string
    {
        return 'new '.$context->writeClassName(self::class).'('.$context->writeValue($this->value).')';
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
