<?php

namespace Keepsuit\Liquid\Tags\Custom;

use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Tags\RenderTag;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class DynamicRenderTag extends RenderTag
{
    protected function allowDynamicPartials(): bool
    {
        return true;
    }
}
