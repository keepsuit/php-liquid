<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Compiler\CompilerContext;

/**
 * A value that can rebuild itself from a plain constructor call.
 *
 * The compiler otherwise falls back to VarExporter, which reconstructs an object
 * graph by writing properties directly. That works for any shape but costs a
 * hydration pass every time the compiled template is instantiated, so the values
 * that dominate a real template describe themselves instead.
 *
 * What gets rebuilt is what the compiled template reads: sub-nodes the compiler
 * has already turned into code (a condition body, a loop body) are not part of
 * the expression, because nothing evaluates them through the object again.
 */
interface CanBeExported
{
    /**
     * A PHP expression that reconstructs this value, or null to let the compiler
     * fall back to VarExporter.
     *
     * Nested values must go through $context->writeValue() so they get the same
     * treatment.
     */
    public function export(CompilerContext $context): ?string;
}
