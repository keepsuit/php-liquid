<?php

namespace Keepsuit\Liquid\Drops;

use Attribute;

/**
 * This attribute can be used to mark a drop method as hidden,
 * so it won't be exposed to the liquid context.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Hidden
{
}
