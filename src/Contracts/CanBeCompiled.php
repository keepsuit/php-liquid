<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Compiler\CompilerContext;

interface CanBeCompiled
{
    /**
     * Return a PHP expression that renders this value, or null to use the
     * compiler's runtime fallback.
     */
    public function compile(CompilerContext $context): ?string;
}
