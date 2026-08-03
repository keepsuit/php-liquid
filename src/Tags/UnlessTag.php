<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;

class UnlessTag extends IfTag
{
    protected ?Condition $unlessCondition;

    public static function tagName(): string
    {
        return 'unless';
    }

    public function parse(TagParseContext $context): static
    {
        parent::parse($context);

        if ($context->tag === static::tagName()) {
            $this->unlessCondition = array_shift($this->conditions);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $result = $this->unlessCondition?->evaluate($context);

        if (! $result) {
            return $this->unlessCondition?->body?->render($context) ?? '';
        }

        return parent::render($context);
    }

    public function compile(CompilerContext $context): void
    {
        if ($this->unlessCondition !== null) {
            $conditionValue = $context->writeRuntimeValue($this->unlessCondition);
            $context->write('if (! '.$conditionValue.'->evaluate($context)) {');
            $context->indent();

            if ($this->unlessCondition->body !== null) {
                $context->compileBody($this->unlessCondition->body);
            }

            $context->outdent()->write('}');
        }

        $this->compileConditions($context, $this->conditions, false);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->unlessCondition, ...$this->conditions];
    }
}
