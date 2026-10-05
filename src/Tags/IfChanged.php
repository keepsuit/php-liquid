<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class IfChanged extends TagBlock
{
    protected BodyNode $body;

    public static function tagName(): string
    {
        return 'ifchanged';
    }

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);

        $this->body = $context->body;

        try {
            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), 'ifchanged', $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $output = $this->body->render($context);

        if ($context->getRegister('ifchanged') === $output) {
            return '';
        }

        $context->setRegister('ifchanged', $output);

        return $output;
    }

    /** @internal */
    public function compileNative(CompilerContext $context): void
    {
        if ($this->body::class !== BodyNode::class) {
            $context->compileFallback($this);

            return;
        }

        $output = $context->temporaryVariable();
        $context->writeRenderedBody($this->body, $output);
        $context->write('if ($context->getRegister("ifchanged") !== '.$output.') {')->indent();
        $context->write('$context->setRegister("ifchanged", '.$output.');');
        $context->writeOutput($output);
        $context->outdent()->write('}');
    }
}
