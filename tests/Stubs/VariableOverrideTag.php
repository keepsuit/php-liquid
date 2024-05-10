<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class VariableOverrideTag extends TagBlock
{
    protected BodyNode $body;

    protected Variable $variable;

    protected mixed $value;

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);
        $this->body = $context->body;

        $this->variable = $context->params->variable();

        $this->value = $context->params->expression();

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return $context->stack(function (RenderContext $context) {
            $name = $this->variable->name;

            $context->set(match (true) {
                $name instanceof VariableLookup, is_string($name) => (string) $name,
                default => throw new SyntaxException('Invalid variable name'),
            }, $this->value);

            return $this->body->render($context);
        });
    }

    public static function tagName(): string
    {
        return 'override';
    }
}
