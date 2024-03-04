<?php

namespace Keepsuit\Liquid\Drops;

use Attribute;

/**
 * This attribute can be used to mark a drop method as cachable.
 * The result of the method will be computed only once and then cached.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Cache
{
}
