<?php

namespace Keepsuit\Liquid\Support;

/**
 * @internal
 */
class Str
{
    private const CACHE_MAX_ENTRIES = 1024;

    private const CACHE_MAX_KEY_LENGTH = 256;

    protected static array $snakeCache = [];

    protected static array $camelCache = [];

    protected static array $studlyCache = [];

    /**
     * Convert a string to snake case.
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $delimiter.':'.$value;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (! ctype_lower($value)) {
            $original = $value;

            if (($value = preg_replace('/\s+/u', '', ucwords($value))) === null) {
                return $original;
            }

            if (($value = preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value)) === null) {
                return $original;
            }

            $value = static::lower($value);
        }

        return static::remember(static::$snakeCache, $key, $value);
    }

    /**
     * Convert a string to camel case.
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::remember(static::$camelCache, $value, lcfirst(static::studly($value)));
    }

    /**
     * Convert a value to studly caps case.
     */
    public static function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $words = explode(' ', str_replace(['-', '_'], ' ', $value));

        $studlyWords = array_map(fn ($word) => ucfirst($word), $words);

        return static::remember(static::$studlyCache, $key, implode($studlyWords));
    }

    /**
     * Caches keyed by template or data names must stay bounded: long-running workers
     * would otherwise retain every distinct name they ever see.
     *
     * @template T
     *
     * @param  T  $value
     * @return T
     */
    public static function remember(array &$cache, string $key, mixed $value): mixed
    {
        if (strlen($key) > self::CACHE_MAX_KEY_LENGTH) {
            return $value;
        }

        if (count($cache) >= self::CACHE_MAX_ENTRIES) {
            $cache = [];
        }

        $cache[$key] = $value;

        return $value;
    }

    /**
     * Convert the given string to lower-case.
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert the given string to upper-case.
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Returns the portion of the string specified by the start and length parameters.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length);
    }

    /**
     * Return the length of the given string.
     */
    public static function length(string $string): int
    {
        return mb_strlen($string);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     */
    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     */
    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Check if the given string contains only empty space.
     */
    public static function blank(string $value): bool
    {
        return trim($value) === '';
    }
}
