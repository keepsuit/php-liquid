<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Compiler\CompilerContext;

/**
 * A value that can rebuild itself from a plain constructor call.
 *
 * The compiler otherwise falls back to native PHP serialization, which
 * reconstructs an object graph every time the compiled template is instantiated.
 * Values that dominate a real template should describe themselves instead.
 *
 * What gets rebuilt is what the compiled template reads: sub-nodes the compiler
 * has already turned into code (a condition body, a loop body) are not part of
 * the expression, because nothing evaluates them through the object again.
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
