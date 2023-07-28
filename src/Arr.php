<?php

namespace Keepsuit\Liquid;

class Arr
{
    public static function flatten(array $array, int $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    public static function compact(array $array): array
    {
        return array_values(array_filter($array, fn ($item) => $item !== null));
    }
}
