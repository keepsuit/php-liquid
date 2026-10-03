<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Render\RenderContext;

class DecrementTag extends IncrementTag
{
    protected string $variableName;

    public static function tagName(): string
    {
        return 'decrement';
    }

    public function render(RenderContext $context): string
    {
        $counter = $context->getData($this->variableName);

        $counter = is_int($counter) ? $counter - 1 : -1;

        $context->setData($this->variableName, $counter);

        return (string) $counter;
    }

    /** @internal */
    public function compileNative(CompilerContext $context): void
    {
        $counter = $context->temporaryVariable();
        $name = $context->writeValue($this->variableName);
        $context->write($counter.' = $context->getData('.$name.');');
        $context->write($counter.' = is_int('.$counter.') ? '.$counter.' - 1 : -1;');
        $context->write('$context->setData('.$name.', '.$counter.');');
        $context->writeOutput('(string) '.$counter);
    }
}
