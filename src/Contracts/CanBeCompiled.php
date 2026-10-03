<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Compiler\CompilerContext;

interface CanBeCompiled
{
    /**
     * Emit generated PHP statements through the compiler context.
     */
    public function compile(CompilerContext $context): void;
}
