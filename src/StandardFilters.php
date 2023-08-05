<?php

namespace Keepsuit\Liquid;

class StandardFilters
{
    /**
     * Returns the absolute value of a number.
     */
    public static function abs(int|float|string $input): int|float
    {
        assert(is_numeric($input));

        return abs($input);
    }

    /**
     * Adds a given string to the end of a string.
     */
    public static function append(string $input, string $append): string
    {
        return $input.$append;
    }

    /**
     * Limits a number to a minimum value.
     */
    public static function atLeast(int|float|string $input, int|float $minValue): int|float
    {
        return max($minValue, static::castToNumber($input));
    }

    /**
     * Limits a number to a maximum value.
     */
    public static function atMost(int|float|string $input, int|float $maxValue): int|float
    {
        return min($maxValue, static::castToNumber($input));
    }

    /**
     * Encodes a string to [Base64 format](https://developer.mozilla.org/en-US/docs/Glossary/Base64).
     */
    public static function base64Encode(?string $input): string
    {
        return base64_encode($input ?? '');
    }

    /**
     * Decodes a string in [Base64 format](https://developer.mozilla.org/en-US/docs/Glossary/Base64).
     */
    public static function base64Decode(?string $input): string
    {
        $decoded = base64_decode($input ?? '', true);

        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid base64 string provided to base64_decode filter');
        }

        return $decoded;
    }

    /**
     * Capitalizes the first word in a string and downcases the remaining characters.
     */
    public static function capitalize(string $input): string
    {
        return Str::upper(Str::substr($input, 0, 1)).Str::lower(Str::substr($input, 1));
    }

    /**
     * Rounds a number up to the nearest integer.
     */
    public static function ceil(int|float|string $input): int
    {
        return (int) ceil(static::castToNumber($input));
    }

    /**
     * Removes any `nil` items from an array.
     */
    public static function compact(array $input, string $property = null): array
    {
        return Arr::compact(static::mapToLiquid($input), $property);
    }

    /**
     * Concatenates (combines) two arrays.
     */
    public static function concat(array $input, array $join): array
    {
        return static::mapToLiquid([...$input, ...$join]);
    }

    public static function date($input)
    {

    }

    public static function default($input)
    {

    }

    public static function dividedBy($input)
    {

    }

    /**
     * Converts a string to all lowercase characters.
     */
    public static function downcase(?string $input): string
    {
        return Str::lower($input ?? '');
    }

    /**
     * Escapes special characters in HTML, such as `<>`, `'`, and `&`, and converts characters into escape sequences.
     * The filter doesn't effect characters within the string that don’t have a corresponding escape sequence.
     */
    public static function escape(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        return htmlentities($input, ENT_QUOTES);
    }

    /**
     * Escape a string once, keeping all previous HTML entities intact
     */
    public static function escapeOnce(string $input): string
    {
        return htmlentities($input, double_encode: false);
    }

    public static function first($input)
    {

    }

    public static function floor($input)
    {

    }

    /**
     * Combines all of the items in an array into a single string, separated by a space.
     */
    public static function join(array $input, string $glue = ' '): string
    {
        return implode($glue, static::mapToLiquid($input));
    }

    public static function last($input)
    {

    }

    public static function lstrip($input)
    {

    }

    /**
     * Creates an array of values from a specific property of the items in an array.
     */
    public static function map(array $input, string $property = null): array
    {
        return Arr::map(static::mapToLiquid($input), $property);
    }

    public static function minus($input)
    {

    }

    public static function modulo($input)
    {

    }

    public static function newlineToBr($input)
    {

    }

    public static function plus($input)
    {

    }

    public static function prepend($input)
    {

    }

    public static function remove($input)
    {

    }

    public static function removeFirst($input)
    {

    }

    public static function replace($input)
    {

    }

    public static function replaceFirst($input)
    {

    }

    /**
     * Reverses the order of the items in an array.
     */
    public static function reverse(array $input): array
    {
        return array_reverse(static::mapToLiquid($input));
    }

    public static function round($input)
    {

    }

    public static function rstrip($input)
    {

    }

    /**
     * Returns the size of an array or a string.
     */
    public static function size(string|array|null $input): int
    {
        if ($input === null) {
            return 0;
        }

        return is_array($input) ? count($input) : Str::length($input);
    }

    /**
     * Returns a substring or series of array items, starting at a given 0-based index.
     */
    public static function slice(string|array|null $input, int $start, int $length = 1): string|array
    {
        $count = static::size($input);

        if (abs($start) >= $count) {
            return is_array($input) ? [] : '';
        }

        if (is_array($input)) {
            return array_slice($input, $start, $length);
        }

        return Str::substr($input ?? '', $start, $length);
    }

    /**
     * Sorts the items in an array in case-sensitive alphabetical, or numerical, order.
     */
    public static function sort(array $input, string $property = null): array
    {
        $result = $property === null ? static::mapToLiquid($input) : Arr::map(static::mapToLiquid($input), $property);

        uasort($result, function ($a, $b) {
            return match (true) {
                $a === $b => 0,
                $a === null => 1,
                $b === null => -1,
                is_string($a) && is_string($b) => strcmp($a, $b),
                default => $a <=> $b,
            };
        });

        foreach (array_keys($result) as $key) {
            $result[$key] = $input[$key];
        }

        return array_values($result);
    }

    /**
     * Sorts the items in an array in case-insensitive alphabetical order.
     */
    public static function sortNatural(array $input, string $property = null): array
    {
        $result = $property === null ? static::mapToLiquid($input) : Arr::map(static::mapToLiquid($input), $property);

        uasort($result, function ($a, $b) {
            return match (true) {
                $a === $b => 0,
                $a === null => 1,
                $b === null => -1,
                default => strcasecmp($a, $b),
            };
        });

        foreach (array_keys($result) as $key) {
            $result[$key] = $input[$key];
        }

        return array_values($result);
    }

    /**
     * Splits a string into an array of substrings based on a given separator.
     *
     * @param  non-empty-string  $delimiter
     */
    public static function split(?string $input, string $delimiter): array
    {
        if ($input === null) {
            return [];
        }

        return explode($delimiter, $input);
    }

    public static function strip($input)
    {

    }

    /**
     * Strips all HTML tags from a string.
     */
    public static function stripHtml(?string $input): string
    {
        $STRIP_HTML_TAGS = '/<[\S\s]*?>/m';
        $STRIP_HTLM_BLOCKS = '/((<script.*?<\/script>)|(<!--.*?-->)|(<style.*?<\/style>))/m';

        return preg_replace([$STRIP_HTLM_BLOCKS, $STRIP_HTML_TAGS], '', $input ?? '');
    }

    public static function stripNewlines($input)
    {

    }

    public static function sum($input)
    {

    }

    public static function times($input)
    {

    }

    /**
     * Truncates a string down to a given number of characters.
     */
    public static function truncate(?string $input, int $length = 50, string $ellipsis = '...'): string
    {
        if ($input === null) {
            return '';
        }

        if (Str::length($input) <= $length) {
            return $input;
        }

        return Str::substr($input, 0, max(0, ($length - Str::length($ellipsis)))).$ellipsis;
    }

    /**
     * Truncates a string down to a given number of words.
     */
    public static function truncatewords(?string $input, int $words = 15, string $ellipsis = '...'): string
    {
        if ($input === null) {
            return '';
        }

        $words = max(1, $words);

        $wordlist = mb_split('\s+', $input, $words + 1);

        if ($wordlist === false) {
            return $input;
        }

        if (count($wordlist) <= $words) {
            return $input;
        }

        array_pop($wordlist);

        return implode(' ', $wordlist).$ellipsis;
    }

    /**
     * Removes any duplicate items in an array.
     */
    public static function uniq(array $input, string $property = null): array
    {
        return Arr::unique(static::mapToLiquid($input), $property);
    }

    /**
     * Converts a string to all uppercase characters.
     */
    public static function upcase(?string $input): string
    {
        return Str::upper($input ?? '');
    }

    /**
     * Decodes any [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding) characters in a string.
     */
    public static function urlDecode(?string $input): string
    {
        return urldecode($input ?? '');
    }

    /**
     * Converts any URL-unsafe characters in a string to the
     * [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding) equivalent.
     */
    public static function urlEncode(string|int|float|null $input): string
    {
        return urlencode((string) ($input ?? ''));
    }

    public static function where($input)
    {

    }

    protected static function castToNumber(string|int|float $input): int|float
    {
        if (is_string($input)) {
            assert(is_numeric($input), 'Input must be numeric.');

            return (float) $input;
        }

        return $input;
    }

    protected static function mapToLiquid(array $input): array
    {
        return Arr::map($input, fn (mixed $value) => $value instanceof RendersToLiquid ? $value->toLiquid() : $value);
    }
}
