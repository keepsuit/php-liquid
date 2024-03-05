<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Attributes\Cache;
use Keepsuit\Liquid\Attributes\Hidden;
use ReflectionClass;
use ReflectionMethod;
use Traversable;

final class DropMetadata
{
    /**
     * @var array<string,mixed>
     */
    protected static array $cache = [];

    public static function init(Drop $drop): DropMetadata
    {
        if (isset(self::$cache[get_class($drop)])) {
            return self::$cache[get_class($drop)];
        }

        $blacklist = array_map(
            fn (ReflectionMethod $method) => $method->getName(),
            (new ReflectionClass(Drop::class))->getMethods(ReflectionMethod::IS_PUBLIC)
        );

        if ($drop instanceof Traversable) {
            $blacklist = [...$blacklist, 'current', 'next', 'key', 'valid', 'rewind'];
        }

        $publicMethods = (new ReflectionClass($drop))->getMethods(ReflectionMethod::IS_PUBLIC);

        $visibleMethodNames = array_map(
            fn (ReflectionMethod $method) => $method->getAttributes(Hidden::class) !== [] ? null : $method->getName(),
            $publicMethods
        );

        $invokableMethods = array_values(array_filter(
            array_diff($visibleMethodNames, $blacklist),
            fn (?string $name) => $name !== null && ! str_starts_with($name, '__')
        ));

        $cacheableMethods = array_values(array_filter(array_map(
            fn (ReflectionMethod $method) => $method->getAttributes(Cache::class) !== [] ? $method->getName() : null,
            $publicMethods
        )));

        return self::$cache[get_class($drop)] = new DropMetadata(
            invokableMethods: $invokableMethods,
            cacheableMethods: $cacheableMethods
        );
    }

    public function __construct(
        public readonly array $invokableMethods,
        public readonly array $cacheableMethods
    ) {
    }
}
