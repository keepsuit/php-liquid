<?php

namespace Keepsuit\Liquid\Attributes;

use Attribute;

/**
 * This attribute can be used to hide:
 * - a drop method, so it won't be exposed to the liquid context.
 * - a FiltersProvider method, so it won't be registered as a filter.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Hidden {}
