<?php

namespace Keepsuit\Liquid\Support;

/**
 * @internal
 */
final readonly class DropStaticProperty
{
    public function __construct(
        public string $name,
        public DropStaticPropertyType $type,
        public bool $cacheable
    ) {}

    public static function property(string $name): DropStaticProperty
    {
        return new DropStaticProperty(name: $name, type: DropStaticPropertyType::Property, cacheable: false);
    }

    public static function method(string $name, bool $cacheable): DropStaticProperty
    {
        return new DropStaticProperty(name: $name, type: DropStaticPropertyType::Method, cacheable: $cacheable);
    }
}
