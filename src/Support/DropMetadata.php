<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Attributes\Cache;
use Keepsuit\Liquid\Attributes\DropDynamicProperties;
use Keepsuit\Liquid\Attributes\Hidden;
use Keepsuit\Liquid\Drop;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Traversable;

/**
 * @internal
 */
final class DropMetadata
{
    /**
     * @var array<string,mixed>
     */
    protected static array $cache = [];

    protected static array $dropBaseMethods;

    public function __construct(
        /** @var list<string> */
        public readonly array $invokableMethods = [],
        /** @var list<string> */
        public readonly array $cacheableMethods = [],
        /** @var list<string> */
        public readonly array $properties = [],
        /** @var list<string> */
        public readonly array $dynamicProperties = [],
    ) {}

    public static function init(Drop $drop): DropMetadata
    {
        if (isset(self::$cache[get_class($drop)])) {
            return self::$cache[get_class($drop)];
        }

        self::$dropBaseMethods ??= array_map(
            fn (ReflectionMethod $method) => $method->getName(),
            (new ReflectionClass(Drop::class))->getMethods(ReflectionMethod::IS_PUBLIC)
        );

        $blacklist = self::$dropBaseMethods;

        if ($drop instanceof Traversable) {
            $blacklist = [...$blacklist, 'current', 'next', 'key', 'valid', 'rewind'];
        }

        $classReflection = new ReflectionClass($drop);

        $publicMethods = array_filter(
            $classReflection->getMethods(ReflectionMethod::IS_PUBLIC),
            fn (ReflectionMethod $method) => ! $method->isStatic()
                && $method->getAttributes(Hidden::class) === []
                && ! in_array($method->getName(), $blacklist)
                && ! str_starts_with($method->getName(), '__')
                && $method->getNumberOfParameters() === 0
        );

        $invokableMethods = array_map(
            fn (ReflectionMethod $method) => $method->getName(),
            $publicMethods
        );

        $cacheableMethods = array_map(
            fn (ReflectionMethod $method) => $method->getName(),
            array_filter(
                $publicMethods,
                fn (ReflectionMethod $method) => $method->getAttributes(Cache::class) !== []
            )
        );

        $publicProperties = array_map(
            fn (ReflectionProperty $property) => $property->getName(),
            array_filter(
                $classReflection->getProperties(ReflectionProperty::IS_PUBLIC),
                fn (ReflectionProperty $property) => $property->getAttributes(Hidden::class) === []
            )
        );

        $dynamicProperties = array_unique(array_merge(...array_map(fn (\ReflectionAttribute $attribute) => $attribute->getArguments()[0], [
            ...$classReflection->getAttributes(DropDynamicProperties::class),
            ...$classReflection->getMethod('liquidMethodMissing')->getAttributes(DropDynamicProperties::class) ?? [],
        ])));

        return self::$cache[get_class($drop)] = new DropMetadata(
            invokableMethods: array_values($invokableMethods),
            cacheableMethods: array_values($cacheableMethods),
            properties: array_values($publicProperties),
            dynamicProperties: array_values($dynamicProperties),
        );
    }
}
