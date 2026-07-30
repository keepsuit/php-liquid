<?php

namespace Keepsuit\Liquid\Support;

/**
 * @internal
 */
final readonly class DropMemberResolution
{
    private function __construct(
        public string $name,
        public DropMemberType $type,
        public bool $cacheable
    ) {}

    public static function property(string $name): self
    {
        return new self(name: $name, type: DropMemberType::Property, cacheable: false);
    }

    public static function method(string $name, bool $cacheable): self
    {
        return new self(name: $name, type: DropMemberType::Method, cacheable: $cacheable);
    }
}
