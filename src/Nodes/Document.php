<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Render\RenderContext;

class Document extends Node implements CanBeCompiled, CanBeStreamed
{
    public function __construct(
        public readonly BodyNode $body,
        public readonly ?string $name = null,
    ) {}

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        return $this->body->render($context);
    }

    public function compile(CompilerContext $context): ?string
    {
        return $context->compileNode($this->body);
    }

    /**
     * @throws LiquidException
     */
    public function stream(RenderContext $context): \Generator
    {
        yield from $this->body->stream($context);
    }

    /**
     * @return array<Node>
     */
    public function children(): array
    {
        return [$this->body];
    }
}
