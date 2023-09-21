<?php

namespace Keepsuit\Liquid\Support;

use Generator;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

/**
 * @mixin Tag
 */
trait AsyncRenderingTag
{
    use GeneratorToString;

    public function render(Context $context): string
    {
        return $this->generatorToString($this->renderAsync($context));
    }

    /**
     * @return Generator<string>
     */
    public function renderAsync(Context $context): Generator
    {
        yield '';
    }
}
