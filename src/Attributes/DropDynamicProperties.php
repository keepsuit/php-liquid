<?php

namespace Keepsuit\Liquid\Attributes;

use Attribute;

/**
 * This attribute can be used on Drop classes
 * to find out properties handled by liquidMethodMissing method.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class DropDynamicProperties
{
    public function __construct(
        /** @var list<string> */
        public readonly array $properties,
    ) {}
}
