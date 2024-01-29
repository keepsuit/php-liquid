<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Render\RenderContext;

class Document implements CanBeRendered
{
    public function __construct(
        protected BodyNode $body,
    ) {
    }

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
     * @return array<Node>
     */
    public function children(): array
    {
        return $this->body->children();
    }
}
