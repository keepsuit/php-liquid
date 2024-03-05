<?php

namespace Keepsuit\Liquid\Attributes;

use Attribute;

/**
 * This attribute can be used to mark a drop method as hidden,
 * so it won't be exposed to the liquid context.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Hidden
{
}
