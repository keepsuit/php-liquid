<?php

namespace Keepsuit\Liquid\Tags\Custom;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Tags\RenderTag;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class DynamicRenderTag extends RenderTag
{
    public function compile(CompilerContext $context): void
    {
        if (is_string($this->templateNameExpression)) {
            $this->compilePartial($context, $context->writeValue($this->templateNameExpression));

            return;
        }

        // loadPartial evaluates the lookup once; returned evaluators must not
        // be resolved again before validating that the name is a string.
        $source = $this->templateNameExpression::class === VariableLookup::class
            ? $context->writeVariableExpression($this->templateNameExpression)
            : $context->writeCachedValue($this->templateNameExpression).'->evaluate($context)';
        $name = $context->temporaryVariable();
        $context->write($name.' = '.$source.';');
        $context->write('if (! is_string('.$name.')) {')->indent();
        $context->write('throw new \\'.SyntaxException::class.'("Template name must be a string");');
        $context->outdent()->write('}');
        $this->compilePartial($context, $name);
    }

    protected function allowDynamicPartials(): bool
    {
        return true;
    }
}
