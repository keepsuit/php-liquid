<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Render\RenderContext;

class Template extends AbstractTemplate
{
    public function __construct(
        public readonly Document $root,
        TemplateSharedState $state = new TemplateSharedState,
    ) {
        parent::__construct($state);
    }

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        try {
            $this->prepareContext($context);

            return $this->root->render($context);
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator
    {
        try {
            $this->prepareContext($context);

            yield from $this->root->stream($context);
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    public function name(): ?string
    {
        return $this->root->name;
    }
}
