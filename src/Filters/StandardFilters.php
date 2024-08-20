<?php

namespace Keepsuit\Liquid\Filters;

use DateTime;
use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\Str;
use Traversable;

class StandardFilters extends FiltersProvider
{
    /**
     * Returns the absolute value of a number.
     */
    public function abs(int|float $input): int|float
    {
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
    public function atLeast(int|float $input, int|float $minValue): int|float
    {
        return max($minValue, $input);
    }

    /**
     * Limits a number to a maximum value.
     */
    public function atMost(int|float $input, int|float $maxValue): int|float
    {
        return min($maxValue, $input);
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
    public function ceil(int|float $input): int
    {
        return (int) ceil($input);
    }

    /**
     * Removes any `nil` items from an array.
     */
    public function compact(array $input, ?string $property = null): array
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
    public function date(DateTime|string|int|null $input, ?string $format = null): DateTime|string|int|null
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

    /**
     * Sets a default value for any variable whose value is one of the following:
     * - `null`
     * - `false`
     * - An empty array
     * - An empty string
     */
    public function default(mixed $input, mixed $defaultValue, bool $allow_false = false): mixed
    {
        $inputValue = $input instanceof AsLiquidValue ? $input->toLiquidValue() : $input;

        return match (true) {
            $inputValue === null, $inputValue === '', $inputValue === [] => $defaultValue,
            $inputValue === false => $allow_false ? $input : $defaultValue,
            default => $input,
        };
    }

    /**
     * Divides a number by a given number.
     * The `divided_by` filter produces a result of the same type as the divisor.
     * This means if you divide by an integer, the result will be an integer,
     * and if you divide by a float, the result will be a float.
     */
    public function dividedBy(int|float $input, int|float $operand): int|float
    {
        $result = $input / $operand;

        return is_int($input) && is_int($operand) ? (int) $result : $result;
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
    public function first(iterable $input): mixed
    {
        $input = $this->mapToLiquid($input);

        if (count($input) === 0) {
            return null;
        }

        if (! array_is_list($input)) {
            return null;
        }

        return $input[0] ?? null;
    }

    /**
     * Rounds a number down to the nearest integer.
     */
    public function floor(int|float $input): int
    {
        return (int) floor($input);
    }

    /**
     * Combines all the items in an array into a single string, separated by a space.
     */
    public function join(iterable $input, string $glue = ' '): string
    {
        return implode($glue, $this->mapToLiquid($input));
    }

    /**
     * Returns the last item in an array.
     */
    public function last(iterable $input): mixed
    {
        $input = $this->mapToLiquid($input);

        if (count($input) === 0) {
            return null;
        }

        return $input[count($input) - 1];
    }

    /**
     * Creates an array of values from a specific property of the items in an array.
     */
    public function map(iterable|Drop $input, string $property): mixed
    {
        if ($input instanceof Drop) {
            if ($input instanceof Traversable) {
                return $this->map(iterator_to_array($input), $property);
            }

            return $input->$property;
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

    /**
     * Subtracts a given number from another number.
     */
    public function minus(int|float $input, int|float $operand): int|float
    {
        return $input - $operand;
    }

    /**
     * Returns the remainder of dividing a number by a given number.
     */
    public function modulo(int|float $input, int|float $operand): int|float
    {
        return $input % $operand;
    }

    /**
     * Converts newlines (`\n`) in a string to HTML line breaks (`<br>`).
     */
    public function newlineToBr(?string $input): string
    {
        return preg_replace('/\r?\n/', "<br />\n", $input ?? '') ?? $input ?? '';
    }

    /**
     * Adds two numbers.
     */
    public function plus(int|float $input, int|float $operand): int|float
    {
        return $input + $operand;
    }

    /**
     * Adds a given string to the beginning of a string.
     */
    public function prepend(string $input, string $prepend): string
    {
        return $prepend.$input;
    }

    /**
     * Removes any instance of a substring inside a string.
     */
    public function remove(string $input, string $search): string
    {
        return $this->replace($input, $search, '');
    }

    /**
     * Removes the first instance of a substring inside a string.
     */
    public function removeFirst(string $input, string $search): string
    {
        return $this->replaceFirst($input, $search, '');
    }

    /**
     * Removes the last instance of a substring inside a string.
     */
    public function removeLast(string $input, string $search): string
    {
        return $this->replaceLast($input, $search, '');
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
    public function reverse(iterable $input): array
    {
        return array_reverse($this->mapToLiquid($input));
    }

    /**
     * Rounds a number to the nearest integer or to the requested decimal places.
     */
    public function round(int|float $input, int $precision = 0): int|float
    {
        return round($input, $precision);
    }

    /**
     * Returns the size of an array or a string.
     */
    public function size(string|iterable|null $input): int
    {
        if ($input === null) {
            return 0;
        }

        if (is_iterable($input) && ! is_array($input)) {
            $input = iterator_to_array($input);
        }

        return is_array($input) ? count($input) : Str::length($input);
    }

    /**
     * Returns a substring or series of array items, starting at a given 0-based index.
     */
    public function slice(string|iterable|null $input, int $start, int $length = 1): string|array
    {
        if (is_iterable($input) && ! is_array($input)) {
            $input = iterator_to_array($input);
        }

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
    public function sort(mixed $input, ?string $property = null): array
    {
        $input = match (true) {
            is_array($input) && ! array_is_list($input) => [$input],
            is_array($input) => $input,
            is_iterable($input) => iterator_to_array($input),
            default => [$input],
        };

        $input = $this->mapToLiquid($input);

        $result = $property === null ? $input : Arr::map($input, $property);

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
    public function sortNatural(mixed $input, ?string $property = null): array
    {
        $input = match (true) {
            is_array($input) && ! array_is_list($input) => [$input],
            is_array($input) => $input,
            is_iterable($input) => iterator_to_array($input),
            default => [$input],
        };

        $input = $this->mapToLiquid($input);

        $result = $property === null ? $input : Arr::map($input, $property);

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

        if ($delimiter === '') {
            return str_split($input);
        }

        return explode($delimiter, $input);
    }

    /**
     * Strips all whitespace from the left and right of a string.
     */
    public function strip(?string $input): string
    {
        return trim($input ?? '');
    }

    /**
     * Strips all whitespace from the left and right of a string.
     */
    public function lstrip(?string $input): string
    {
        return ltrim($input ?? '');
    }

    /**
     * Strips all whitespace from the left and right of a string.
     */
    public function rstrip(?string $input): string
    {
        return rtrim($input ?? '');
    }

    /**
     * Strips all HTML tags from a string.
     */
    public function stripHtml(?string $input): string
    {
        $STRIP_HTML_TAGS = '/<[\S\s]*?>/m';
        $STRIP_HTLM_BLOCKS = '/((<script.*?<\/script>)|(<!--.*?-->)|(<style.*?<\/style>))/m';

        return preg_replace([$STRIP_HTLM_BLOCKS, $STRIP_HTML_TAGS], '', $input ?? '') ?? $input ?? '';
    }

    /**
     * Strips all newline characters (line breaks) from a string.
     */
    public function stripNewlines(?string $input): string
    {
        return preg_replace('/\r?\n/', '', $input ?? '') ?? $input ?? '';
    }

    /**
     * Returns the sum of all elements in an array.
     */
    public function sum(iterable $input, ?string $property = null): int|float
    {
        $input = $this->mapToLiquid($input);

        if ($input === []) {
            return 0;
        }

        $values = array_filter(
            $property !== null ? $this->mapToLiquid(Arr::map($input, $property)) : $input,
            fn (mixed $value) => is_numeric($value)
        );

        return array_sum($values);
    }

    /**
     * Multiplies a number by a given number.
     */
    public function times(int|float $input, int|float $operand): int|float
    {
        return $input * $operand;
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
    public function uniq(iterable $input, ?string $property = null): array
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

    /**
     * Filters an array to include only items with a specific property value.
     */
    public function where(iterable $input, ?string $property = null, mixed $targetValue = null): array
    {
        $input = $this->mapToLiquid($input);

        if ($input === []) {
            return [];
        }

        $input = array_is_list($input) ? $input : [$input];

        $result = array_filter($input, function (mixed $value) use ($property, $targetValue) {
            if ($targetValue === null) {
                return match (true) {
                    is_string($value) && $property !== null => str_starts_with($value, $property),
                    is_array($value) && $property !== null => (bool) ($value[$property] ?? null),
                    is_object($value) && $property !== null => (bool) ($value->$property ?? null),
                    default => (bool) $value,
                };
            }

            return match (true) {
                is_array($value) && $property !== null => ($value[$property] ?? null) === $targetValue,
                is_object($value) && $property !== null => ($value->$property ?? null) === $targetValue,
                default => false,
            };
        });

        return array_values($result);
    }

    protected function mapToLiquid(iterable $input): array
    {
        return Arr::map(Arr::from($input), function (mixed $value) {
            $value = $this->context->normalizeValue($value);

            if ($value instanceof IsContextAware) {
                $value->setContext($this->context);
            }

            return $value;
        });
    }
}
