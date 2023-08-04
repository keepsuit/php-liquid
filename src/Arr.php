<?php

namespace Keepsuit\Liquid;

use Closure;

class Arr
{
    public static function first(array $array, Closure $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return $default instanceof Closure ? $default() : $default;
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default instanceof Closure ? $default() : $default;
    }

    public static function flatten(array $array, float $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1.0
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    public static function compact(array $array, Closure|string $callbackOrProperty = null): array
    {
        $filterCallback = match (true) {
            $callbackOrProperty === null => fn (mixed $item) => $item !== null,
            $callbackOrProperty instanceof Closure => $callbackOrProperty,
            default => fn (mixed $item) => static::valueGetter($item, $callbackOrProperty) !== null,
        };

        return array_values(array_filter($array, $filterCallback));
    }

    public static function unique(array $array, Closure|string $callbackOrProperty = null): array
    {
        $result = array_unique($callbackOrProperty === null ? [...$array] : Arr::map($array, $callbackOrProperty));

        foreach (array_keys($result) as $key) {
            $result[$key] = $array[$key];
        }

        return array_values($result);
    }

    public static function map(array $array, Closure|string $callbackOrProperty): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$key] = static::valueGetter($value, $callbackOrProperty);
        }

        return $result;
    }

    protected static function valueGetter(mixed $value, Closure|string $callbackOrProperty): mixed
    {
        return match (true) {
            $callbackOrProperty instanceof Closure => $callbackOrProperty($value),
            is_array($value) => $value[$callbackOrProperty] ?? null,
            is_object($value) => $value->$callbackOrProperty ?? null,
            default => throw new \InvalidArgumentException('Cannot sort by property on non-object, non-array items'),
        };
    }
}
