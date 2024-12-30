<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Render\RenderContext;

class Document extends Node implements CanBeStreamed
{
    public function __construct(
        public readonly BodyNode $body,
    ) {}

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        return $this->body->render($context);
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
