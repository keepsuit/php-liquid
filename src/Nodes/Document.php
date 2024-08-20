<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Render\RenderContext;

class Document implements CanBeRendered, CanBeStreamed
{
    public function __construct(
        protected BodyNode $body,
    ) {}

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        if ($context->getProfiler() !== null) {
            return $context->getProfiler()->profile(
                node: $this->body,
                context: $context,
                templateName: $context->getTemplateName()
            );
        }

        return $this->body->render($context);
    }

    /**
     * @throws LiquidException
     */
    public function stream(RenderContext $context): \Generator
    {
        if ($context->getProfiler() !== null) {
            yield $this->render($context);

            return;
        }

        yield from $this->body->stream($context);
    }

    /**
     * @return array<Node>
     */
    public function children(): array
    {
        return $this->body->children();
    }
}
