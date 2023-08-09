<?php

namespace Keepsuit\Liquid;

use DateTime;
use InvalidArgumentException;
use Iterator;

class StandardFilters
{
    public function __construct(
        protected Context $context
    ) {
    }

    /**
     * Returns the absolute value of a number.
     */
    public function abs(int|float|string $input): int|float
    {
        assert(is_numeric($input));

        return abs($input);
    }

    /**
     * Adds a given string to the end of a string.
     */
    public function append(string $input, string $append): string
    {
        return $input.$append;
    }

    /**
     * Limits a number to a minimum value.
     */
    public function atLeast(int|float|string $input, int|float $minValue): int|float
    {
        return max($minValue, static::castToNumber($input));
    }

    /**
     * Limits a number to a maximum value.
     */
    public function atMost(int|float|string $input, int|float $maxValue): int|float
    {
        return min($maxValue, static::castToNumber($input));
    }

    /**
     * Encodes a string to [Base64 format](https://developer.mozilla.org/en-US/docs/Glossary/Base64).
     */
    public function base64Encode(?string $input): string
    {
        return base64_encode($input ?? '');
    }

    /**
     * Decodes a string in [Base64 format](https://developer.mozilla.org/en-US/docs/Glossary/Base64).
     */
    public function base64Decode(?string $input): string
    {
        $decoded = base64_decode($input ?? '', true);

        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid base64 string provided to base64_decode filter');
        }

        return $decoded;
    }

    /**
     * Capitalizes the first word in a string and downcases the remaining characters.
     */
    public function capitalize(string $input): string
    {
        return Str::upper(Str::substr($input, 0, 1)).Str::lower(Str::substr($input, 1));
    }

    /**
     * Rounds a number up to the nearest integer.
     */
    public function ceil(int|float|string $input): int
    {
        return (int) ceil(static::castToNumber($input));
    }

    /**
     * Removes any `nil` items from an array.
     */
    public function compact(array $input, string $property = null): array
    {
        return Arr::compact($this->mapToLiquid($input), $property);
    }

    /**
     * Concatenates (combines) two arrays.
     */
    public function concat(array $input, array $join): array
    {
        return $this->mapToLiquid([...$input, ...$join]);
    }

    /**
     * Format a date using strftime format.
     *
     *   %a - The abbreviated weekday name (``Sun'')
     *   %A - The  full  weekday  name (``Sunday'')
     *   %b - The abbreviated month name (``Jan'')
     *   %B - The  full  month  name (``January'')
     *   %c - The preferred local date and time representation
     *   %d - Day of the month (01..31)
     *   %H - Hour of the day, 24-hour clock (00..23)
     *   %I - Hour of the day, 12-hour clock (01..12)
     *   %j - Day of the year (001..366)
     *   %m - Month of the year (01..12)
     *   %M - Minute of the hour (00..59)
     *   %p - Meridian indicator (``AM''  or  ``PM'')
     *   %s - Number of seconds since 1970-01-01 00:00:00 UTC.
     *   %S - Second of the minute (00..60)
     *   %U - Week  number  of the current year,
     *           starting with the first Sunday as the first
     *           day of the first week (00..53)
     *   %W - Week  number  of the current year,
     *           starting with the first Monday as the first
     *           day of the first week (00..53)
     *   %w - Day of the week (Sunday is 0, 0..6)
     *   %x - Preferred representation for the date alone, no time
     *   %X - Preferred representation for the time alone, no date
     *   %y - Year without a century (00..99)
     *   %Y - Year with century
     *   %Z - Time zone name
     *   %% - Literal ``%'' character
     */
    public function date(DateTime|string|int|null $input, string $format = null): ?string
    {
        if ($input === null || $input === '') {
            return $input;
        }

        if ($format === null || $format === '') {
            return $input;
        }

        if (is_numeric($input)) {
            $input = date('Y-m-d H:i:s', (int) $input);
        }

        if (is_string($input)) {
            $input = new DateTime($input);
        }

        $dateFormat = str_replace(
            ['at', '%a', '%A', '%d', '%e', '%u', '%w', '%W', '%b', '%h', '%B', '%m', '%y', '%Y', '%D', '%F', '%x', '%n', '%t', '%H', '%k', '%I', '%l', '%M', '%p', '%P', '%r', '%R', '%S', '%T', '%X', '%z', '%Z', '%c', '%s', '%%'],
            ['\a\t', 'D', 'l', 'd', 'j', 'N', 'w', 'W', 'M', 'M', 'F', 'm', 'y', 'Y', 'm/d/y', 'Y-m-d', 'm/d/y', "\n", "\t", 'H', 'G', 'h', 'g', 'i', 'A', 'a', 'h:i:s A', 'H:i', 's', 'H:i:s', 'H:i:s', 'O', 'T', 'D M j H:i:s Y', 'U', '%'],
            $format
        );

        return $input->format($dateFormat);
    }

    public function default($input)
    {

    }

    public function dividedBy($input)
    {

    }

    /**
     * Converts a string to all lowercase characters.
     */
    public function downcase(?string $input): string
    {
        return Str::lower($input ?? '');
    }

    /**
     * Escapes special characters in HTML, such as `<>`, `'`, and `&`, and converts characters into escape sequences.
     * The filter doesn't effect characters within the string that don’t have a corresponding escape sequence.
     */
    public function escape(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        return htmlentities($input, ENT_QUOTES);
    }

    /**
     * Escape a string once, keeping all previous HTML entities intact
     */
    public function escapeOnce(string $input): string
    {
        return htmlentities($input, double_encode: false);
    }

    /**
     * Returns the first item in an array.
     */
    public function first(array|Iterator $input): mixed
    {
        $input = $this->mapToLiquid($input);

        if (count($input) === 0) {
            return null;
        }

        return $input[0];
    }

    public function floor($input)
    {

    }

    /**
     * Combines all of the items in an array into a single string, separated by a space.
     */
    public function join(array $input, string $glue = ' '): string
    {
        return implode($glue, $this->mapToLiquid($input));
    }

    /**
     * Returns the last item in an array.
     */
    public function last(array|Iterator $input): mixed
    {
        $input = $this->mapToLiquid($input);

        if (count($input) === 0) {
            return null;
        }

        return $input[count($input) - 1];
    }

    public function lstrip($input)
    {

    }

    /**
     * Creates an array of values from a specific property of the items in an array.
     */
    public function map(array|Drop|Iterator $input, string $property): mixed
    {
        if ($input instanceof Drop) {
            return match (true) {
                property_exists($input, $property) => $input->$property,
                method_exists($input, $property) => $input->$property(),
                default => throw new InvalidArgumentException(sprintf(
                    'Property or method "%s" does not exist on object of type "%s"',
                    $property,
                    get_class($input)
                ))
            };
        }

        $input = $this->mapToLiquid($input);

        if (array_is_list($input)) {
            return Arr::map($input, $property);
        }

        if (array_key_exists($property, $input)) {
            return $input[$property];
        }

        throw new InvalidArgumentException(sprintf(
            'Property "%s" does not exist on array',
            $property
        ));
    }

    public function minus($input)
    {

    }

    public function modulo($input)
    {

    }

    public function newlineToBr($input)
    {

    }

    public function plus($input)
    {

    }

    public function prepend($input)
    {

    }

    public function remove($input)
    {

    }

    public function removeFirst($input)
    {

    }

    /**
     * Replaces any instance of a substring inside a string with a given string.
     */
    public function replace(string $input, string $search, string $replace): string
    {
        return str_replace($search, $replace, $input);
    }

    /**
     * Replaces the first instance of a substring inside a string with a given string.
     */
    public function replaceFirst(string $input, string $search, string $replace): string
    {
        return Str::replaceFirst($search, $replace, $input);
    }

    /**
     * Replaces the last instance of a substring inside a string with a given string.
     */
    public function replaceLast(string $input, string $search, string $replace): string
    {
        return Str::replaceLast($search, $replace, $input);
    }

    /**
     * Reverses the order of the items in an array.
     */
    public function reverse(array $input): array
    {
        return array_reverse($this->mapToLiquid($input));
    }

    public function round($input)
    {

    }

    public function rstrip($input)
    {

    }

    /**
     * Returns the size of an array or a string.
     */
    public function size(string|array|null $input): int
    {
        if ($input === null) {
            return 0;
        }

        return is_array($input) ? count($input) : Str::length($input);
    }

    /**
     * Returns a substring or series of array items, starting at a given 0-based index.
     */
    public function slice(string|array|null $input, int $start, int $length = 1): string|array
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
    public function sort(array|Iterator $input, string $property = null): array
    {
        $input = Arr::from($input);

        $result = $property === null ? $this->mapToLiquid($input) : Arr::map($this->mapToLiquid($input), $property);

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
    public function sortNatural(array $input, string $property = null): array
    {
        $result = $property === null ? $this->mapToLiquid($input) : Arr::map($this->mapToLiquid($input), $property);

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
    public function split(?string $input, string $delimiter): array
    {
        if ($input === null) {
            return [];
        }

        return explode($delimiter, $input);
    }

    public function strip($input)
    {

    }

    /**
     * Strips all HTML tags from a string.
     */
    public function stripHtml(?string $input): string
    {
        $STRIP_HTML_TAGS = '/<[\S\s]*?>/m';
        $STRIP_HTLM_BLOCKS = '/((<script.*?<\/script>)|(<!--.*?-->)|(<style.*?<\/style>))/m';

        return preg_replace([$STRIP_HTLM_BLOCKS, $STRIP_HTML_TAGS], '', $input ?? '');
    }

    public function stripNewlines($input)
    {

    }

    public function sum($input)
    {

    }

    public function times($input)
    {

    }

    /**
     * Truncates a string down to a given number of characters.
     */
    public function truncate(?string $input, int $length = 50, string $ellipsis = '...'): string
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
    public function truncatewords(?string $input, int $words = 15, string $ellipsis = '...'): string
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
    public function uniq(array $input, string $property = null): array
    {
        return Arr::unique($this->mapToLiquid($input), $property);
    }

    /**
     * Converts a string to all uppercase characters.
     */
    public function upcase(?string $input): string
    {
        return Str::upper($input ?? '');
    }

    /**
     * Decodes any [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding) characters in a string.
     */
    public function urlDecode(?string $input): string
    {
        return urldecode($input ?? '');
    }

    /**
     * Converts any URL-unsafe characters in a string to the
     * [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding) equivalent.
     */
    public function urlEncode(string|int|float|null $input): string
    {
        return urlencode((string) ($input ?? ''));
    }

    public function where($input)
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

    protected function mapToLiquid(array|Iterator $input): array
    {
        return Arr::map(Arr::from($input), function (mixed $value) {
            $value = $value instanceof MapsToLiquid ? $value->toLiquid() : $value;
            if ($value instanceof IsContextAware) {
                $value->setContext($this->context);
            }

            return $value;
        });
    }
}
