<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Compiler\CompilerContext;

/**
 * A value that can rebuild itself from a plain constructor call, instead of being
 * unserialized every time the compiled template is instantiated.
 */
interface CanBeExported
{
    /**
     * A PHP expression that reconstructs this value, or null to let the compiler
     * fall back to native PHP serialization.
     *
     * Nested values must go through $context->writeValue() so they get the same
     * treatment.
     */
    public function export(CompilerContext $context): ?string;
}
