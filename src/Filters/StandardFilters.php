<?php

namespace Keepsuit\Liquid\Filters;

use DateTime;
use DateTimeZone;
use Keepsuit\Liquid\Attributes\Hidden;
use Keepsuit\Liquid\Condition\ConditionOperator;
use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\FilterCoercion;
use Keepsuit\Liquid\Support\FilterSupport;
use Keepsuit\Liquid\Support\Str;
use Keepsuit\Liquid\Support\StrftimeFormatter;
use Keepsuit\Liquid\Support\UndefinedVariable;
use Traversable;

class StandardFilters extends FiltersProvider
{
    protected ?FilterSupport $filterSupport = null;

    #[Hidden]
    public function setContext(RenderContext $context): void
    {
        if (isset($this->context) && $this->context === $context) {
            return;
        }

        $this->context = $context;
        $this->filterSupport = null;
    }

    protected function filterSupport(): FilterSupport
    {
        return $this->filterSupport ??= new FilterSupport($this->context);
    }

    private const HTML_ESCAPE = [
        '&' => '&amp;',
        '<' => '&lt;',
        '>' => '&gt;',
        '"' => '&quot;',
        "'" => '&#39;',
    ];

    /**
     * Returns the absolute value of a number.
     */
    public function abs(mixed $input): int|float
    {
        return abs(FilterCoercion::toNumber($input));
    }

    /**
     * Adds a given string to the end of a string.
     */
    public function append(mixed $input, mixed $append): string
    {
        return FilterCoercion::toString($input).FilterCoercion::toString($append);
    }

    /**
     * Limits a number to a minimum value.
     */
    public function atLeast(mixed $input, mixed $minValue): int|float
    {
        return max(FilterCoercion::toNumber($minValue), FilterCoercion::toNumber($input));
    }

    /**
     * Limits a number to a maximum value.
     */
    public function atMost(mixed $input, mixed $maxValue): int|float
    {
        return min(FilterCoercion::toNumber($maxValue), FilterCoercion::toNumber($input));
    }

    /**
     * Encodes a string to [Base64 format](https://developer.mozilla.org/en-US/docs/Glossary/Base64).
     */
    public function base64Encode(mixed $input): string
    {
        return base64_encode(FilterCoercion::toString($input));
    }

    /**
     * Decodes a string in [Base64 format](https://developer.mozilla.org/en-US/docs/Glossary/Base64).
     */
    public function base64Decode(mixed $input): string
    {
        $input = FilterCoercion::toString($input);
        $decoded = base64_decode($input, true);

        if ($decoded === false || base64_encode($decoded) !== $input) {
            throw new InvalidArgumentException('Invalid base64 string provided to base64_decode filter');
        }

        return $decoded;
    }

    /**
     * Encodes a string in URL-safe Base64 format.
     */
    public function base64UrlSafeEncode(mixed $input): string
    {
        return strtr($this->base64Encode($input), '+/', '-_');
    }

    /**
     * Decodes a string in URL-safe Base64 format, with optional padding.
     */
    public function base64UrlSafeDecode(mixed $input): string
    {
        $input = strtr(FilterCoercion::toString($input), '-_', '+/');

        if (! str_contains($input, '=')) {
            $input .= str_repeat('=', (4 - strlen($input) % 4) % 4);
        }

        $decoded = base64_decode($input, true);

        if ($decoded === false || base64_encode($decoded) !== $input) {
            throw new InvalidArgumentException('Invalid base64 string provided to base64_url_safe_decode filter');
        }

        return $decoded;
    }

    /**
     * Capitalizes the first word in a string and downcases the remaining characters.
     */
    public function capitalize(mixed $input): string
    {
        $input = FilterCoercion::toString($input);

        return Str::upper(Str::substr($input, 0, 1)).Str::lower(Str::substr($input, 1));
    }

    /**
     * Rounds a number up to the nearest integer.
     */
    public function ceil(mixed $input): int
    {
        $input = FilterCoercion::toFiniteNumber($input);

        return is_int($input) ? $input : (int) ceil($input);
    }

    /**
     * Removes any `nil` items from an array.
     */
    public function compact(mixed $input, mixed $property = null): ?array
    {
        $this->filterSupport()->validateProperty($property);
        $result = [];

        foreach ($this->filterSupport()->iterate($input) as $item) {
            if ($property !== null && ! $this->filterSupport()->canSelectProperty($item)) {
                return null;
            }

            if (($property === null ? $item : $this->filterSupport()->property($item, $property)) !== null) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Concatenates (combines) two arrays.
     */
    public function concat(mixed $input, mixed $join): array
    {
        if ($join instanceof UndefinedVariable) {
            throw $join->toException();
        }

        if (! is_array($join) || ! array_is_list($join)) {
            throw new InvalidArgumentException('concat filter requires an array argument');
        }

        return [...$this->filterSupport()->iterate($input), ...$this->filterSupport()->normalizeCollection($join)];
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
    public function date(DateTime|string|int|float|bool|null $input, ?string $format = null): DateTime|string|int|float|bool|null
    {
        if ($input === null || $input === '' || is_float($input) || is_bool($input) || $format === null || $format === '') {
            return $input;
        }

        if (is_string($input) && is_numeric($input) && ! ctype_digit($input)) {
            return $input;
        }

        try {
            $date = match (true) {
                $input instanceof DateTime => $input,
                is_int($input) || ctype_digit($input) => (new DateTime('@'.$input))->setTimezone(new DateTimeZone(date_default_timezone_get())),
                default => new DateTime($input),
            };
        } catch (\Exception|\ValueError) {
            return $input;
        }

        return (new StrftimeFormatter)->format($date, $format);
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
    public function dividedBy(mixed $input, mixed $operand): int|float
    {
        $input = FilterCoercion::toNumber($input);
        $operand = FilterCoercion::toNumber($operand);

        if (is_int($input) && is_int($operand)) {
            if ($input === PHP_INT_MIN && $operand === -1) {
                return -(float) $input;
            }

            $quotient = intdiv($input, $operand);

            return $input % $operand !== 0 && ($input < 0) !== ($operand < 0)
                ? $quotient - 1
                : $quotient;
        }

        return fdiv($input, $operand);
    }

    /**
     * Converts a string to all lowercase characters.
     */
    public function downcase(mixed $input): string
    {
        return Str::lower(FilterCoercion::toString($input));
    }

    /**
     * Escapes special characters in HTML, such as `<>`, `'`, and `&`, and converts characters into escape sequences.
     * The filter doesn't effect characters within the string that don’t have a corresponding escape sequence.
     */
    public function escape(mixed $input): ?string
    {
        if ($input === null) {
            return null;
        }

        return strtr(FilterCoercion::toString($input), self::HTML_ESCAPE);
    }

    /**
     * Alias of escape.
     */
    public function h(mixed $input): ?string
    {
        return $this->escape($input);
    }

    /**
     * Escape a string once, keeping all previous HTML entities intact
     */
    public function escapeOnce(mixed $input): string
    {
        $input = FilterCoercion::toString($input);

        return preg_replace_callback('/[><"\']|&(?!([a-zA-Z]+|#\d+);)/', fn (array $match) => self::HTML_ESCAPE[$match[0]], $input) ?? $input;
    }

    /**
     * Returns the first item in an array.
     */
    public function first(mixed $input): mixed
    {
        if ($input instanceof UndefinedVariable) {
            throw $input->toException();
        }

        if (is_string($input)) {
            return Str::substr($input, 0, 1);
        }

        if (! is_iterable($input)) {
            return null;
        }

        $isHash = is_array($input) && ! array_is_list($input);
        $input = $this->filterSupport()->normalizeCollection($input);

        if (count($input) === 0) {
            return null;
        }

        if (! array_is_list($input)) {
            $key = array_key_first($input);

            return $isHash ? [$key, $input[$key]] : null;
        }

        return $input[0] ?? null;
    }

    /**
     * Rounds a number down to the nearest integer.
     */
    public function floor(mixed $input): int
    {
        $input = FilterCoercion::toFiniteNumber($input);

        return is_int($input) ? $input : (int) floor($input);
    }

    /**
     * Combines all the items in an array into a single string, separated by a space.
     */
    public function join(mixed $input, mixed $glue = ' '): string
    {
        $glue = $this->filterSupport()->stringify($glue);
        $output = '';
        $first = true;

        foreach ($this->filterSupport()->iterate($input) as $value) {
            $output .= ($first ? '' : $glue).$this->filterSupport()->stringify($value);
            $first = false;
        }

        return $output;
    }

    /**
     * Returns the last item in an array.
     */
    public function last(mixed $input): mixed
    {
        if ($input instanceof UndefinedVariable) {
            throw $input->toException();
        }

        if (is_string($input)) {
            return Str::substr($input, -1);
        }

        if (! is_iterable($input)) {
            return null;
        }

        $input = $this->filterSupport()->normalizeCollection($input);

        if (count($input) === 0 || ! array_is_list($input)) {
            return null;
        }

        return $input[count($input) - 1];
    }

    /**
     * Creates an array of values from a specific property of the items in an array.
     */
    public function map(mixed $input, mixed $property): array
    {
        $this->filterSupport()->validateProperty($property);
        $result = [];

        foreach ($this->filterSupport()->iterate($input) as $item) {
            $result[] = $property === 'to_liquid' ? $item : $this->filterSupport()->property($item, $property);
        }

        return $result;
    }

    /**
     * Subtracts a given number from another number.
     */
    public function minus(mixed $input, mixed $operand): int|float
    {
        return FilterCoercion::toNumber($input) - FilterCoercion::toNumber($operand);
    }

    /**
     * Returns the remainder of dividing a number by a given number.
     */
    public function modulo(mixed $input, mixed $operand): int|float
    {
        $input = FilterCoercion::toNumber($input);
        $operand = FilterCoercion::toNumber($operand);

        if ($operand == 0) {
            throw new \DivisionByZeroError;
        }

        $remainder = is_int($input) && is_int($operand)
            ? $input % $operand
            : fmod($input, $operand);

        return $remainder != 0 && ($remainder < 0) !== ($operand < 0)
            ? $remainder + $operand
            : $remainder;
    }

    /**
     * Converts newlines (`\n`) in a string to HTML line breaks (`<br>`).
     */
    public function newlineToBr(mixed $input): string
    {
        $input = FilterCoercion::toString($input);

        return preg_replace('/\r?\n/', "<br />\n", $input) ?? $input;
    }

    /**
     * Adds two numbers.
     */
    public function plus(mixed $input, mixed $operand): int|float
    {
        return FilterCoercion::toNumber($input) + FilterCoercion::toNumber($operand);
    }

    /**
     * Adds a given string to the beginning of a string.
     */
    public function prepend(mixed $input, mixed $prepend): string
    {
        return FilterCoercion::toString($prepend).FilterCoercion::toString($input);
    }

    /**
     * Removes any instance of a substring inside a string.
     */
    public function remove(mixed $input, mixed $search): string
    {
        return $this->replace($input, $search, '');
    }

    /**
     * Removes the first instance of a substring inside a string.
     */
    public function removeFirst(mixed $input, mixed $search): string
    {
        return $this->replaceFirst($input, $search, '');
    }

    /**
     * Removes the last instance of a substring inside a string.
     */
    public function removeLast(mixed $input, mixed $search): string
    {
        return $this->replaceLast($input, $search, '');
    }

    /**
     * Replaces any instance of a substring inside a string with a given string.
     */
    public function replace(mixed $input, mixed $search, mixed $replace = ''): string
    {
        $input = FilterCoercion::toString($input);
        $search = FilterCoercion::toString($search);
        $replace = FilterCoercion::toString($replace);

        if ($search === '') {
            return $input === '' ? $replace : $replace.implode($replace, mb_str_split($input)).$replace;
        }

        return str_replace($search, $replace, $input);
    }

    /**
     * Replaces the first instance of a substring inside a string with a given string.
     */
    public function replaceFirst(mixed $input, mixed $search, mixed $replace = ''): string
    {
        $input = FilterCoercion::toString($input);
        $search = FilterCoercion::toString($search);
        $replace = FilterCoercion::toString($replace);

        return $search === '' ? $replace.$input : Str::replaceFirst($search, $replace, $input);
    }

    /**
     * Replaces the last instance of a substring inside a string with a given string.
     */
    public function replaceLast(mixed $input, mixed $search, mixed $replace): string
    {
        $input = FilterCoercion::toString($input);
        $search = FilterCoercion::toString($search);
        $replace = FilterCoercion::toString($replace);

        return $search === '' ? $input.$replace : Str::replaceLast($search, $replace, $input);
    }

    /**
     * Reverses the order of the items in an array.
     */
    public function reverse(mixed $input): array
    {
        return array_reverse(iterator_to_array($this->filterSupport()->iterate($input)));
    }

    /**
     * Rounds a number to the nearest integer or to the requested decimal places.
     */
    public function round(mixed $input, mixed $precision = 0): int|float
    {
        $input = FilterCoercion::toFiniteNumber($input);
        $precision = (int) FilterCoercion::toFiniteNumber($precision);

        return is_int($input) && $precision >= 0 ? $input : round($input, $precision);
    }

    /**
     * Returns the size of an array or a string.
     */
    public function size(mixed $input): int
    {
        if ($input instanceof UndefinedVariable) {
            throw $input->toException();
        }

        if (is_string($input)) {
            return Str::length($input);
        }

        if ($input instanceof Traversable) {
            $input = iterator_to_array($input);
        }

        return match (true) {
            is_array($input) => count($input),
            is_int($input) => PHP_INT_SIZE,
            default => 0,
        };
    }

    /**
     * Returns a substring or series of array items, starting at a given 0-based index.
     */
    public function slice(mixed $input, mixed $start, mixed $length = 1): string|array
    {
        $start = FilterCoercion::toInteger($start);
        $length = $length === null || $length === false ? 1 : FilterCoercion::toInteger($length);

        if (is_iterable($input) && ! is_array($input)) {
            $input = iterator_to_array($input);
        }

        if (! is_array($input)) {
            $input = FilterCoercion::toString($input);
        }

        $count = static::size($input);

        if ($start < -$count || $start >= $count || $length <= 0) {
            return is_array($input) ? [] : '';
        }

        if (is_array($input)) {
            return array_slice($input, $start, $length);
        }

        return Str::substr($input, $start, $length);
    }

    /**
     * Sorts the items in an array in case-sensitive alphabetical, or numerical, order.
     */
    public function sort(mixed $input, mixed $property = null): ?array
    {
        return $this->sortInput($input, $property, natural: false);
    }

    /**
     * Sorts the items in an array in case-insensitive alphabetical order.
     */
    public function sortNatural(mixed $input, mixed $property = null): ?array
    {
        return $this->sortInput($input, $property, natural: true);
    }

    /**
     * Splits a string into an array of substrings based on a given separator.
     */
    public function split(mixed $input, mixed $delimiter): array
    {
        $input = FilterCoercion::toString($input);
        $delimiter = FilterCoercion::toString($delimiter);

        if ($input === '') {
            return [];
        }

        if ($delimiter === '') {
            return mb_str_split($input);
        }

        if ($delimiter === ' ') {
            return preg_split('/[\x09-\x0d ]+/', $input, flags: PREG_SPLIT_NO_EMPTY) ?: [];
        }

        $parts = explode($delimiter, $input);

        while ($parts !== [] && end($parts) === '') {
            array_pop($parts);
        }

        return $parts;
    }

    /**
     * Strips all whitespace from the left and right of a string.
     */
    public function strip(mixed $input): string
    {
        return trim(FilterCoercion::toString($input));
    }

    /**
     * Strips all whitespace from the left and right of a string.
     */
    public function lstrip(mixed $input): string
    {
        return ltrim(FilterCoercion::toString($input));
    }

    /**
     * Strips all whitespace from the left and right of a string.
     */
    public function rstrip(mixed $input): string
    {
        return rtrim(FilterCoercion::toString($input));
    }

    /**
     * Trims surrounding whitespace and collapses internal whitespace runs to single spaces.
     */
    public function squish(mixed $input): string
    {
        $input = trim(FilterCoercion::toString($input));

        if ($input === '') {
            return '';
        }

        return preg_replace('/\s+/', ' ', $input) ?? $input;
    }

    /**
     * Strips all HTML tags from a string.
     */
    public function stripHtml(mixed $input): string
    {
        $input = FilterCoercion::toString($input);
        $stripHtmlTags = '/<.*?>/s';
        $stripHtmlBlocks = '/<script.*?<\/script>|<!--.*?-->|<style.*?<\/style>/s';

        return preg_replace([$stripHtmlBlocks, $stripHtmlTags], '', $input) ?? $input;
    }

    /**
     * Strips all newline characters (line breaks) from a string.
     */
    public function stripNewlines(mixed $input): string
    {
        $input = FilterCoercion::toString($input);

        return preg_replace('/\r?\n/', '', $input) ?? $input;
    }

    /**
     * Returns the sum of all elements in an array.
     */
    public function sum(mixed $input, mixed $property = null): int|float
    {
        $this->filterSupport()->validateProperty($property);
        $values = [];

        foreach ($this->filterSupport()->iterate($input) as $item) {
            $values[] = $property === null ? $item : $this->filterSupport()->property($item, $property);
        }

        $sum = 0;

        foreach ($this->filterSupport()->iterate($values) as $value) {
            $value = $value instanceof AsLiquidValue ? $value->toLiquidValue() : $value;
            $sum += FilterCoercion::toNumber($value);
        }

        return $sum;
    }

    /**
     * Multiplies a number by a given number.
     */
    public function times(mixed $input, mixed $operand): int|float
    {
        return FilterCoercion::toNumber($input) * FilterCoercion::toNumber($operand);
    }

    /**
     * Truncates a string down to a given number of characters.
     */
    public function truncate(mixed $input, mixed $length = 50, mixed $ellipsis = '...'): string
    {
        if ($length instanceof UndefinedVariable) {
            throw $length->toException();
        }

        $ellipsis = FilterCoercion::toString($ellipsis);

        if ($input === null) {
            return '';
        }

        $input = FilterCoercion::toString($input);
        $length = FilterCoercion::toInteger($length);

        if (Str::length($input) <= $length) {
            return $input;
        }

        return Str::substr($input, 0, max(0, ($length - Str::length($ellipsis)))).$ellipsis;
    }

    /**
     * Truncates a string down to a given number of words.
     */
    public function truncatewords(mixed $input, mixed $words = 15, mixed $ellipsis = '...'): string
    {
        if ($words instanceof UndefinedVariable) {
            throw $words->toException();
        }

        $ellipsis = FilterCoercion::toString($ellipsis);

        if ($input === null) {
            return '';
        }

        $input = FilterCoercion::toString($input);

        $words = max(1, FilterCoercion::toInteger($words));

        if ($words >= PHP_INT_MAX) {
            return $input;
        }

        $wordlist = preg_split('/[\x09-\x0d ]+/', ltrim($input, " \t\n\r\v\f"), $words + 1);

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
    public function uniq(mixed $input, mixed $property = null): ?array
    {
        $this->filterSupport()->validateProperty($property);
        $input = iterator_to_array($this->filterSupport()->iterate($input));
        if (count($input) < 2) {
            return $input;
        }

        $result = [];
        $seen = [];

        foreach ($input as $item) {
            $value = $item instanceof AsLiquidValue ? $item->toLiquidValue() : $item;
            if ($property !== null && ! $this->filterSupport()->canSelectProperty($value)) {
                return null;
            }
            $value = $property === null ? $value : $this->filterSupport()->property($value, $property);

            $key = $this->uniqueKey($value);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Converts a string to all uppercase characters.
     */
    public function upcase(mixed $input): string
    {
        return Str::upper(FilterCoercion::toString($input));
    }

    /**
     * Decodes any [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding) characters in a string.
     */
    public function urlDecode(mixed $input): string
    {
        return urldecode(FilterCoercion::toString($input));
    }

    /**
     * Converts any URL-unsafe characters in a string to the
     * [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding) equivalent.
     */
    public function urlEncode(mixed $input): string
    {
        return urlencode(FilterCoercion::toString($input));
    }

    /**
     * Filters an array to include only items with a specific property value.
     */
    public function where(mixed $input, mixed $property, mixed $targetValue = null): ?array
    {
        return $this->filterArray($input, $property, $targetValue, 'where');
    }

    /**
     * Filters an array to exclude items with a specific property value.
     */
    public function reject(mixed $input, mixed $property, mixed $targetValue = null): ?array
    {
        return $this->filterArray($input, $property, $targetValue, 'reject');
    }

    /**
     * Tests if any item in an array has a specific property value.
     */
    public function has(mixed $input, mixed $property, mixed $targetValue = null): ?bool
    {
        return $this->filterArray($input, $property, $targetValue, 'has');
    }

    /**
     * Returns the first item in an array with a specific property value.
     */
    public function find(mixed $input, mixed $property, mixed $targetValue = null): mixed
    {
        return $this->filterArray($input, $property, $targetValue, 'find');
    }

    /**
     * Returns the index of the first item in an array with a specific property value.
     */
    public function findIndex(mixed $input, mixed $property, mixed $targetValue = null): ?int
    {
        return $this->filterArray($input, $property, $targetValue, 'findIndex');
    }

    protected function sortInput(mixed $input, mixed $property, bool $natural): ?array
    {
        $this->filterSupport()->validateProperty($property);
        $input = iterator_to_array($this->filterSupport()->iterate($input));

        if ($property !== null) {
            foreach ($input as $item) {
                if (! $this->filterSupport()->canSelectProperty($item)) {
                    return null;
                }
            }
        }

        if (count($input) < 2) {
            return $input;
        }

        $values = [];
        foreach ($input as $item) {
            $values[] = $property === null ? $item : $this->filterSupport()->property($item, $property);
        }

        uasort($values, function (mixed $a, mixed $b) use ($natural): int {
            if ($a === null || $b === null) {
                return $a === $b ? 0 : ($a === null ? 1 : -1);
            }

            return $natural
                ? strcasecmp($this->filterSupport()->stringify($a), $this->filterSupport()->stringify($b))
                : $this->compareArrayValues($a, $b);
        });

        return array_map(fn (int $key) => $input[$key], array_keys($values));
    }

    protected function compareArrayValues(mixed $a, mixed $b): int
    {
        if ($a === $b) {
            return 0;
        }

        if ((is_int($a) || is_float($a)) && (is_int($b) || is_float($b))) {
            return $a <=> $b;
        }

        if (is_string($a) && is_string($b)) {
            return strcmp($a, $b);
        }

        if (is_array($a) && is_array($b)) {
            if ($this->arrayValuesEqual($a, $b)) {
                return 0;
            }

            if (array_is_list($a) && array_is_list($b)) {
                foreach ($a as $key => $value) {
                    if (! array_key_exists($key, $b)) {
                        break;
                    }
                    $comparison = $this->compareArrayValues($value, $b[$key]);
                    if ($comparison !== 0) {
                        return $comparison;
                    }
                }

                return count($a) <=> count($b);
            }
        }

        throw new InvalidArgumentException('cannot sort values of incompatible types');
    }

    protected function filterArray(mixed $input, mixed $property, mixed $targetValue, string $filter): mixed
    {
        $this->filterSupport()->validateProperty($property);
        if ($targetValue instanceof UndefinedVariable) {
            throw $targetValue->toException();
        }

        $targetValue = $targetValue instanceof AsLiquidValue ? $targetValue->toLiquidValue() : $targetValue;
        $result = [];

        foreach ($this->filterSupport()->iterate($input) as $index => $item) {
            if (! $this->filterSupport()->canSelectProperty($item)) {
                return null;
            }

            $value = $this->filterSupport()->property($item, $property);
            $value = $value instanceof AsLiquidValue ? $value->toLiquidValue() : $value;
            $matches = $targetValue === null
                ? $value !== false && $value !== null
                : ConditionOperator::Equal->evaluate($value, $targetValue);

            if ($filter === 'reject' ? ! $matches : $matches) {
                if ($filter === 'has') {
                    return true;
                }
                if ($filter === 'find') {
                    return $item;
                }
                if ($filter === 'findIndex') {
                    return $index;
                }
                $result[] = $item;
            }
        }

        return match ($filter) {
            'has' => false,
            'find', 'findIndex' => null,
            default => $result,
        };
    }

    protected function uniqueKey(mixed $value): string
    {
        if (is_object($value)) {
            return 'object:'.spl_object_id($value);
        }

        if (is_array($value)) {
            ksort($value);

            return 'array:'.serialize(array_map($this->uniqueKey(...), $value));
        }

        if (is_resource($value)) {
            return 'resource:'.get_resource_id($value);
        }

        // Ruby's eql? distinguishes numeric types, but not the sign of zero.
        if (is_float($value) && $value == 0) {
            $value = 0.0;
        }

        return serialize($value);
    }

    protected function arrayValuesEqual(mixed $left, mixed $right): bool
    {
        if (ConditionOperator::Equal->evaluate($left, $right)) {
            return true;
        }

        if (! is_array($left) || ! is_array($right) || count($left) !== count($right)) {
            return false;
        }

        foreach ($left as $key => $value) {
            if (! array_key_exists($key, $right) || ! $this->arrayValuesEqual($value, $right[$key])) {
                return false;
            }
        }

        return true;
    }
}
